@extends('admin.admin')

@section('title')
     게시판 {{ $title }} | {{ $homePageConfig->title }}
@endsection

@section('include_script')
    <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script>
    var menuVal = 300100;
    $(function(){
        // 복사 버튼 클릭
        $(".board_copy").click(function(){
            window.open(this.href, "win_board_copy", "left=100,top=100,width=550,height=450");
            return false;
        });
    });
    // 환경설정에 입력된 포인트로 설정 함수
    function set_point(f) {
        if (f.config_env_point.checked) {
            // alert(f.read_point.defaultValue);
            f.read_point.value = {{ $boardConfig->readPoint }};
            f.write_point.value = {{ $boardConfig->writePoint }};
            f.comment_point.value = {{ $boardConfig->commentPoint }};
            f.download_point.value = {{ $boardConfig->downloadPoint }};
        } else {
            f.read_point.value     = f.read_point.defaultValue;
            f.write_point.value    = f.write_point.defaultValue;
            f.comment_point.value  = f.comment_point.defaultValue;
            f.download_point.value = f.download_point.defaultValue;
        }
    }

    tinymce.init({
        selector: '.editorArea',
        language: 'ko_KR',
        branding: false,
        theme: "modern",
        skin: "lightgray",
        height: 400,
        min_height: 400,
        min_width: 750,
        selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
        plugins: 'link,autolink,image,imagetools,textcolor,lists,pagebreak,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,code',
        toolbar: "undo redo | styleselect | forecolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link customImage media code",
        relative_urls: false,
        setup: function(editor) {
            editor.addButton('customImage', {
                text: '사진',
                icon: 'image',
                onclick: function () {
                    window.open('{{ route('image.form') }}','tinymcePop','width=640, height=480');
                }
            });
        }
    });
    </script>
@endsection

@section('content')
<div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if ($errors->any())
                <script>
                    alert("{{ $errors->first() }}");
                </script>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading"><h2>게시판 {{ $title }}</h2></div>
                <form class="form-horizontal" role="form" method="POST" action="{{ $action }}">
                    {{ csrf_field() }}
                @if($type == 'edit')
                    {{ method_field('PUT') }}
                @endif
                <section id="anc_basic">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 기본 설정</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                                <a class="btn" href="#anc_extra">여분 필드</a>
                            </p>
                        </tr>
                        <tr>
                            <th>TABLE</th>
                            <td @if($errors->get('table_name')) class="has-error" @endif>
                                @if($type == 'edit')
                                    <input type="text" name="table_name" value="{{ $board->table_name }}" maxlength="20" readonly />
                                    <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                    <a class="btn btn-primary" href="{{ route('admin.boards.index')}}">목록으로</a>
                                @else
                                    <input type="text" name="table_name" required/>
                                    영문자, 숫자, _ 만 가능(공백없이 20자 이내)
                                @endif
                                @foreach ($errors->get('table_name') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>그룹</th>
                            <td>
                                <select name="group_id" required>
                                    <option value>선택</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}"
                                            @if($type == 'edit' && $group->id == $board->group_id) selected
                                            @elseif($type == 'create' && $group->id == $selectedGroup) selected
                                            @endif>
                                            {{ $group->subject }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>게시판 제목</th>
                            <td @if($errors->get('subject')) class="has-error" @endif>
                                <input type="text" name="subject" @if($type == 'edit') value="{{ $board->subject }}" @endif required/>
                                @foreach ($errors->get('subject') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>모바일 게시판 제목</th>
                            <td>
                                모바일에서 보여지는 게시판 제목이 다른 경우에 입력합니다. 입력이 없으면 기본 게시판 제목이 출력됩니다.<br />
                                <input type="text" name="mobile_subject" @if($type == 'edit') value="{{ $board->mobile_subject }}" @endif />
                            </td>
                        </tr> --}}
                        <tr>
                            <th>접속기기</th>
                            <td>
                                PC 와 모바일 사용을 구분합니다.<br />
                                <select name="device">
                                    <option value="both" @if($type == 'edit' && $board->device == 'both') selected @endif>PC와 모바일에서 모두 사용</option>
                                    <option value="pc" @if($type == 'edit' && $board->device == 'pc') selected @endif>PC 전용</option>
                                    <option value="mobile" @if($type == 'edit' && $board->device == 'mobile') selected @endif>모바일 전용</option>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_device" name="chk_group_device" value="1" />
                                <label for="chk_group_device">그룹적용</label>
                                <input type="checkbox" id="chk_all_device" name="chk_all_device" value="1" />
                                <label for="chk_all_device">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>분류</th>
                            <td>
                                분류와 분류 사이는 | 로 구분하세요. (예: 질문|답변) 첫자로 #은 입력하지 마세요. (예: #질문|#답변 [X])<br />
                                <input type="text" name="category_list" @if($type == 'edit') value="{{ $board->category_list }}" @endif />
                                <input type="checkbox" id="use_category" name="use_category" value="1"
                                    @if($type == 'edit' && $board->use_category == '1') checked @endif />
                                <label for="use_category">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_category_list" name="chk_group_category_list" value="1" />
                                <label for="chk_group_category_list">그룹적용</label>
                                <input type="checkbox" id="chk_all_category_list" name="chk_all_category_list" value="1" />
                                <label for="chk_all_category_list">전체적용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                            @if($type == 'edit')
                                <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                    게시판 복사
                                </a>
                                <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                <a class="btn btn-primary" href="">게시판 썸네일 삭제</a>
                            @endif
                        </div>
                    </div>
                </section>
                <section id="anc_auth">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 권한 설정</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                                <a class="btn" href="#anc_extra">여분 필드</a>
                            </p>
                        </tr>
                        <tr>
                            <th>게시판 관리자</th>
                            <td>
                                <input type="text" name="admin" @if($type == 'edit') value="{{ $board->admin }}" @endif />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_admin" name="chk_group_admin" value="1" />
                                <label for="chk_group_admin">그룹적용</label>
                                <input type="checkbox" id="chk_all_admin" name="chk_all_admin" value="1" />
                                <label for="chk_all_admin">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록보기 권한</th>
                            <td>
                                <select name="list_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->list_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_list_level" name="chk_group_list_level" value="1" />
                                <label for="chk_group_list_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_list_level" name="chk_all_list_level" value="1" />
                                <label for="chk_all_list_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>글읽기 권한</th>
                            <td>
                                <select name="read_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->read_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_read_level" name="chk_group_read_level" value="1" />
                                <label for="chk_group_read_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_read_level" name="chk_all_read_level" value="1" />
                                <label for="chk_all_read_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>글쓰기 권한</th>
                            <td>
                                <select name="write_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->write_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_write_level" name="chk_group_write_level" value="1" />
                                <label for="chk_group_write_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_write_level" name="chk_all_write_level" value="1" />
                                <label for="chk_all_write_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>글답변 권한</th>
                            <td>
                                <select name="reply_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->reply_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_reply_level" name="chk_group_reply_level" value="1" />
                                <label for="chk_group_reply_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_reply_level" name="chk_all_reply_level" value="1" />
                                <label for="chk_all_reply_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>댓글쓰기 권한</th>
                            <td>
                                <select name="comment_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->comment_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_comment_level" name="chk_group_comment_level" value="1" />
                                <label for="chk_group_comment_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_comment_level" name="chk_all_comment_level" value="1" />
                                <label for="chk_all_comment_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>링크 권한</th>
                            <td>
                                <select name="link_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->link_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_link_level" name="chk_group_link_level" value="1" />
                                <label for="chk_group_link_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_link_level" name="chk_all_link_level" value="1" />
                                <label for="chk_all_link_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>업로드 권한</th>
                            <td>
                                <select name="upload_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->upload_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_upload_level" name="chk_group_upload_level" value="1" />
                                <label for="chk_group_upload_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_upload_level" name="chk_all_upload_level" value="1" />
                                <label for="chk_all_upload_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>다운로드 권한</th>
                            <td>
                                <select name="download_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->download_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_download_level" name="chk_group_download_level" value="1" />
                                <label for="chk_group_download_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_download_level" name="chk_all_download_level" value="1" />
                                <label for="chk_all_download_level">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>HTML 쓰기 권한</th>
                            <td>
                                <select name="html_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($type == 'edit' && $board->html_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_html_level" name="chk_group_html_level" value="1" />
                                <label for="chk_group_html_level">그룹적용</label>
                                <input type="checkbox" id="chk_all_html_level" name="chk_all_html_level" value="1" />
                                <label for="chk_all_html_level">전체적용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                            @if($type == 'edit')
                                <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                    게시판 복사
                                </a>
                                <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                <a class="btn btn-primary" href="">게시판 썸네일 삭제</a>
                            @endif
                        </div>
                    </div>
                </section>
                <section id="anc_function">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 기능 설정</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                                <a class="btn" href="#anc_extra">여분 필드</a>
                            </p>
                        </tr>
                        <tr>
                            <th>원글 수정 불가</th>
                            <td>
                                댓글의 수가 설정 수 이상이면 원글을 수정할 수 없습니다. 0으로 설정하시면 댓글 수에 관계없이 수정할 수있습니다.<br />
                                댓글<input type="text" name="count_modify" value="{{ $board['count_modify'] }}" required />개 이상 달리면 수정불가
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_count_modify" name="chk_group_count_modify" value="1" />
                                <label for="chk_group_count_modify">그룹적용</label>
                                <input type="checkbox" id="chk_all_count_modify" name="chk_all_count_modify" value="1" />
                                <label for="chk_all_count_modify">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>원글 삭제 불가</th>
                            <td>
                                댓글<input type="text" name="count_delete" value="{{ $board['count_delete'] }}" required />개 이상 달리면 삭제불가
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_count_delete" name="chk_group_count_delete" value="1" />
                                <label for="chk_group_count_delete">그룹적용</label>
                                <input type="checkbox" id="chk_all_count_delete" name="chk_all_count_delete" value="1" />
                                <label for="chk_all_count_delete">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>글쓴이 사이드뷰</th>
                            <td>
                                <input type="checkbox" id="use_sideview" name="use_sideview" value="1"
                                    @if($type == 'edit' && $board->use_sideview == 1 ) checked @endif />
                                <label for="use_sideview">사용(글쓴이 클릭시 나오는 레이어 메뉴)</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_sideview" name="chk_group_use_sideview" value="1" />
                                <label for="chk_group_use_sideview">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_sideview" name="chk_all_use_sideview" value="1" />
                                <label for="chk_all_use_sideview">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>비밀글 사용</th>
                            <td>
                                "체크박스"는 글작성시 비밀글 체크가 가능합니다. "무조건"은 작성되는 모든글을 비밀글로 작성합니다. (관리자는 체크박스로 출력합니다.) 스킨에 따라 적용되지 않을 수 있습니다.<br />
                                <select name="use_secret">
                                    <option value='0' @if($board['use_secret'] == 0) selected @endif >사용하지 않음</option>
                                    <option value='1' @if($board['use_secret'] == 1) selected @endif >체크박스</option>
                                    <option value='2' @if($board['use_secret'] == 2) selected @endif >무조건</option>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_secret" name="chk_group_use_secret" value="1" />
                                <label for="chk_group_use_secret">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_secret" name="chk_all_use_secret" value="1" />
                                <label for="chk_all_use_secret">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>DHTML 에디터 사용</th>
                            <td>
                                글작성시 내용을 DHTML 에디터 기능으로 사용할 것인지 설정합니다. 스킨에 따라 적용되지 않을 수 있습니다.<br />
                                <input type="checkbox" id="use_dhtml_editor" name="use_dhtml_editor" value="1"
                                    @if($type == 'edit' && $board->use_dhtml_editor == 1 ) checked @endif />
                                <label for="use_dhtml_editor">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_dhtml_editor" name="chk_group_use_dhtml_editor" value="1" />
                                <label for="chk_group_use_dhtml_editor">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_dhtml_editor" name="chk_all_use_dhtml_editor" value="1" />
                                <label for="chk_all_use_dhtml_editor">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>RSS 보이기 사용</th>
                            <td>
                                비회원 글읽기가 가능하고 RSS 보이기 사용에 체크가 되어야만 RSS 지원을 합니다.<br />
                                <input type="checkbox" id="use_rss_view" name="use_rss_view" value="1"
                                    @if($type == 'edit' && $board->use_rss_view == 1 ) checked @endif />
                                <label for="use_rss_view">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_rss_view" name="chk_group_use_rss_view" value="1" />
                                <label for="chk_group_use_rss_view">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_rss_view" name="chk_all_use_rss_view" value="1" />
                                <label for="chk_all_use_rss_view">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>회원 자동입력방지(구글리캡챠) 사용</th>
                            <td>
                                <input type="checkbox" id="use_recaptcha" name="use_recaptcha" value="1"
                                    @if($type == 'edit' && $board->use_recaptcha == 1 ) checked @endif />
                                <label for="use_recaptcha">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_rss_view" name="chk_group_use_rss_view" value="1" />
                                <label for="chk_group_use_rss_view">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_rss_view" name="chk_all_use_rss_view" value="1" />
                                <label for="chk_all_use_rss_view">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_good" name="use_good" value="1"
                                    @if($type == 'edit' && $board->use_good == 1 ) checked @endif />
                                <label for="use_good">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_good" name="chk_group_use_good" value="1" />
                                <label for="chk_group_use_good">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_good" name="chk_all_use_good" value="1" />
                                <label for="chk_all_use_good">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>비추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_nogood" name="use_nogood" value="1"
                                    @if($type == 'edit' && $board->use_nogood == 1 ) checked @endif />
                                <label for="use_nogood">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_nogood" name="chk_group_use_nogood" value="1" />
                                <label for="chk_group_use_nogood">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_nogood" name="chk_all_use_nogood" value="1" />
                                <label for="chk_all_use_nogood">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>이름(실명) 사용</th>
                            <td>
                                <input type="checkbox" id="use_name" name="use_name" value="1"
                                    @if($type == 'edit' && $board->use_name == 1 ) checked @endif />
                                <label for="use_name">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_name" name="chk_group_use_name" value="1" />
                                <label for="chk_group_use_name">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_name" name="chk_all_use_name" value="1" />
                                <label for="chk_all_use_name">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>서명보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_signature" name="use_signature" value="1"
                                    @if($type == 'edit' && $board->use_signature == 1 ) checked @endif />
                                <label for="use_signature">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_signature" name="chk_group_use_signature" value="1" />
                                <label for="chk_group_use_signature">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_signature" name="chk_all_use_signature" value="1" />
                                <label for="chk_all_use_signature">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>IP 보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_ip_view" name="use_ip_view" value="1"
                                    @if($type == 'edit' && $board->use_ip_view == 1 ) checked @endif />
                                <label for="use_ip_view">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_ip_view" name="chk_group_use_ip_view" value="1" />
                                <label for="chk_group_use_ip_view">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_ip_view" name="chk_all_use_ip_view" value="1" />
                                <label for="chk_all_use_ip_view">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록에서 내용 사용</th>
                            <td>
                                목록에서 게시판 제목외에 내용도 읽어와야 할 경우에 설정하는 옵션입니다. 기본은 사용하지 않습니다.<br />
                                <input type="checkbox" id="use_list_content" name="use_list_content" value="1"
                                    @if($type == 'edit' && $board->use_list_content == 1 ) checked @endif />
                                <label for="use_list_content">사용(사용시 속도가 느려질 수 있습니다.)</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_list_content" name="chk_group_use_list_content" value="1" />
                                <label for="chk_group_use_list_content">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_list_content" name="chk_all_use_list_content" value="1" />
                                <label for="chk_all_use_list_content">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록에서 파일 사용</th>
                            <td>
                                목록에서 게시판 첨부파일을 읽어와야 할 경우에 설정하는 옵션입니다. 기본은 사용하지 않습니다.<br />
                                <input type="checkbox" id="use_list_file" name="use_list_file" value="1"
                                    @if($type == 'edit' && $board->use_list_file == 1 ) checked @endif />
                                <label for="use_list_file">사용(사용시 속도가 느려질 수 있습니다.)</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_list_file" name="chk_group_use_list_file" value="1" />
                                <label for="chk_group_use_list_file">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_list_file" name="chk_all_use_list_file" value="1" />
                                <label for="chk_all_use_list_file">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>전체목록보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_list_view" name="use_list_view" value="1"
                                    @if($type == 'edit' && $board->use_list_view == 1 ) checked @endif />
                                <label for="use_list_view">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_list_view" name="chk_group_use_list_view" value="1" />
                                <label for="chk_group_use_list_view">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_list_view" name="chk_all_use_list_view" value="1" />
                                <label for="chk_all_use_list_view">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>메일발송 사용</th>
                            <td>
                                <input type="checkbox" id="use_email" name="use_email" value="1"
                                    @if($type == 'edit' && $board->use_email == 1 ) checked @endif />
                                <label for="use_email">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_email" name="chk_group_use_email" value="1" />
                                <label for="chk_group_use_email">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_email" name="chk_all_use_email" value="1" />
                                <label for="chk_all_use_email">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>본인확인 사용</th>
                            <td>
                                본인확인 여부에 따라 게시물을 조회 할 수 있도록 합니다.<br />
                                    <select name="use_cert">
                                        <option value="not-use" selected>사용안함</option>
                                        {{-- <option value="cert">본인확인된 회원전체</option>
                                        <option value="adult">본인확인된 성인회원만</option>
                                        <option value="hp-cert">휴대폰 본인확인된 회원전체</option>
                                        <option value="hp-adult">휴대폰 본인확인된 성인회원만</option> --}}
                                        <!-- 환경 설정의 본인확인 설정에 따라서 option이 변경됨. -->
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_cert" name="chk_group_use_cert" value="1" />
                                <label for="chk_group_use_cert">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_cert" name="chk_all_use_cert" value="1" />
                                <label for="chk_all_use_cert">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>파일 업로드 개수</th>
                            <td>
                                게시물 한건당 업로드 할 수 있는 파일의 최대 개수 (0 은 파일첨부 사용하지 않음)<br />
                                <input type="text" name="upload_count" value="{{ $board['upload_count'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_upload_count" name="chk_group_upload_count" value="1" />
                                <label for="chk_group_upload_count">그룹적용</label>
                                <input type="checkbox" id="chk_all_upload_count" name="chk_all_upload_count" value="1" />
                                <label for="chk_all_upload_count">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>파일 업로드 용량</th>
                            <td>
                                최대 1024M 이하 업로드 가능, 1 MB = 1,048,576 bytes<br />
                                업로드 파일 한개당<input type="text" name="upload_size" value="{{ $board['upload_size'] }}" required />bytes 이하
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_upload_size" name="chk_group_upload_size" value="1" />
                                <label for="chk_group_upload_size">그룹적용</label>
                                <input type="checkbox" id="chk_all_upload_size" name="chk_all_upload_size" value="1" />
                                <label for="chk_all_upload_size">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>파일 설명 사용</th>
                            <td>
                                <input type="checkbox" id="use_file_content" name="use_file_content" value="1"
                                    @if($type == 'edit' && $board->use_file_content == 1 ) checked @endif />
                                <label for="use_file_content">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_file_content" name="chk_group_use_file_content" value="1" />
                                <label for="chk_group_use_file_content">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_file_content" name="chk_all_use_file_content" value="1" />
                                <label for="chk_all_use_file_content">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>최소 글수 제한</th>
                            <td>
                                글 입력시 최소 글자수를 설정. 0을 입력하거나 최고관리자, DHTML 에디터 사용시에는 검사하지 않음<br />
                                <input type="text" name="write_min" @if($type == 'edit') value="{{ $board->write_min }}" @endif />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_write_min" name="chk_group_write_min" value="1" />
                                <label for="chk_group_write_min">그룹적용</label>
                                <input type="checkbox" id="chk_all_write_min" name="chk_all_write_min" value="1" />
                                <label for="chk_all_write_min">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>최대 글수 제한</th>
                            <td>
                                글 입력시 최대 글자수를 설정. 0을 입력하거나 최고관리자, DHTML 에디터 사용시에는 검사하지 않음<br />
                                <input type="text" name="write_max" @if($type == 'edit') value="{{ $board->write_max }}" @endif />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_write_max" name="chk_group_write_max" value="1" />
                                <label for="chk_group_write_max">그룹적용</label>
                                <input type="checkbox" id="chk_all_write_max" name="chk_all_write_max" value="1" />
                                <label for="chk_all_write_max">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>최소 댓글수 제한</th>
                            <td>
                                댓글 입력시 최소 글자수를 설정. 0을 입력하면 검사하지 않음<br />
                                <input type="text" name="comment_min" @if($type == 'edit') value="{{ $board->comment_min }}" @endif />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_comment_min" name="chk_group_comment_min" value="1" />
                                <label for="chk_group_comment_min">그룹적용</label>
                                <input type="checkbox" id="chk_all_comment_min" name="chk_all_comment_min" value="1" />
                                <label for="chk_all_comment_min">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>최대 댓글수 제한</th>
                            <td>
                                댓글 입력시 최대 글자수를 설정. 0을 입력하면 검사하지 않음<br />
                                <input type="text" name="comment_max" @if($type == 'edit') value="{{ $board->comment_max }}" @endif />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_comment_max" name="chk_group_comment_max" value="1" />
                                <label for="chk_group_comment_max">그룹적용</label>
                                <input type="checkbox" id="chk_all_comment_max" name="chk_all_comment_max" value="1" />
                                <label for="chk_all_comment_max">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>SNS 사용</th>
                            <td>
                                사용에 체크하시면 소셜네트워크서비스(SNS)에 글을 퍼가거나 댓글을 동시에 등록할수 있습니다.<br />
                                기본환경설정의 SNS 설정을 하셔야 사용이 가능합니다.<br />
                                <input type="checkbox" id="use_sns" name="use_sns" value="1"
                                    @if($type == 'edit' && $board->use_sns == 1 ) checked @endif />
                                <label for="use_sns">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_sns" name="chk_group_use_sns" value="1" />
                                <label for="chk_group_use_sns">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_sns" name="chk_all_use_sns" value="1" />
                                <label for="chk_all_use_sns">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>전체 검색 사용</th>
                            <td>
                                <input type="checkbox" id="use_search" name="use_search" value="1" @if($board['use_search'] == 1 ) checked @endif />
                                <label for="use_search">사용</label>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_use_search" name="chk_group_use_search" value="1" />
                                <label for="chk_group_use_search">그룹적용</label>
                                <input type="checkbox" id="chk_all_use_search" name="chk_all_use_search" value="1" />
                                <label for="chk_all_use_search">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>출력 순서</th>
                            <td>
                                숫자가 낮은 게시판 부터 메뉴나 검색시 우선 출력합니다.<br />
                                <input type="text" id="order" name="order" @if($type == 'edit') value="{{ $board->order }}" @endif />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_order" name="chk_group_order" value="1" />
                                <label for="chk_group_order">그룹적용</label>
                                <input type="checkbox" id="chk_all_order" name="chk_all_order" value="1" />
                                <label for="chk_all_order">전체적용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                            @if($type == 'edit')
                                <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                    게시판 복사
                                </a>
                                <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                <a class="btn btn-primary" href="">게시판 썸네일 삭제</a>
                            @endif
                        </div>
                    </div>
                </section>
                <section id="anc_design">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 디자인/양식</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                                <a class="btn" href="#anc_extra">여분 필드</a>
                            </p>
                        </tr>
                        <tr>
                            <th>스킨 디렉토리</th>
                            <td>
                                <select id="skin" name="skin" required>
                                    @foreach ($skins as $skin)
                                        <option @if($board['skin'] == $skin) selected @endif value="{{ $skin }}">
                                            {{ $skin }}
                                        </option>
                                    @endforeach
                                    </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_skin" name="chk_group_skin" value="1" />
                                <label for="chk_group_skin">그룹적용</label>
                                <input type="checkbox" id="chk_all_skin" name="chk_all_skin" value="1" />
                                <label for="chk_all_skin">전체적용</label>
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>모바일 스킨 디렉토리</th>
                            <td>
                                <select id="mobile_skin" name="mobile_skin" required>
                                    @foreach ($mobileSkins as $skin)
                                        <option @if($board['mobile_skin'] == $skin) selected @endif value="{{ $skin }}">
                                            {{ $skin }}
                                        </option>
                                    @endforeach
                                    </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_skin" name="chk_group_mobile_skin" value="1" />
                                <label for="chk_group_mobile_skin">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_skin" name="chk_all_mobile_skin" value="1" />
                                <label for="chk_all_mobile_skin">전체적용</label>
                            </td>
                        </tr> --}}
                        <tr>
                            <th>레이아웃 파일 경로</th>
                            <td>
                                resources/views/layouts 이하의 경로로 확장자 빼고 입력해주세요.<br />
                                <input type="text" id="layout" name="layout" value="{{ $board['layout'] }}" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_layout" name="chk_group_layout" value="1" />
                                <label for="chk_group_layout">그룹적용</label>
                                <input type="checkbox" id="chk_all_layout" name="chk_all_layout" value="1" />
                                <label for="chk_all_layout">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>상단 내용</th>
                            <td>
                                <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                                    <textarea name="content_head" id="content_head" class="editorArea">{{ $board['content_head'] }}</textarea>
                                </div>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_content_head" name="chk_group_content_head" value="1" />
                                <label for="chk_group_content_head">그룹적용</label>
                                <input type="checkbox" id="chk_all_content_head" name="chk_all_content_head" value="1" />
                                <label for="chk_all_content_head">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>하단 내용</th>
                            <td>
                                <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                                    <textarea name="content_tail" id="content_tail" class="editorArea">{{ $board['content_tail'] }}</textarea>
                                </div>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_content_tail" name="chk_group_content_tail" value="1" />
                                <label for="chk_group_content_tail">그룹적용</label>
                                <input type="checkbox" id="chk_all_content_tail" name="chk_all_content_tail" value="1" />
                                <label for="chk_all_content_tail">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>글쓰기 기본 내용</th>
                            <td>
                                <div style="border: 1px solid #ccc; background: #fff; min-height: 400px; border-radius: 4px; box-sizing: border-box;">
                                <textarea name="insert_content" rows="5">{{ $board['insert_content'] }}</textarea>
                                </div>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_insert_content" name="chk_group_insert_content" value="1" />
                                <label for="chk_group_insert_content">그룹적용</label>
                                <input type="checkbox" id="chk_all_insert_content" name="chk_all_insert_content" value="1" />
                                <label for="chk_all_insert_content">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>제목 길이</th>
                            <td>
                                목록에서의 제목 글자수. 잘리는 글은 … 로 표시<br />
                                <input type="text" name="subject_len" value="{{ $board['subject_len'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_subject_len" name="chk_group_subject_len" value="1" />
                                <label for="chk_group_subject_len">그룹적용</label>
                                <input type="checkbox" id="chk_all_subject_len" name="chk_all_subject_len" value="1" />
                                <label for="chk_all_subject_len">전체적용</label>
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>모바일 제목 길이</th>
                            <td>
                                목록에서의 제목 글자수. 잘리는 글은 … 로 표시<br />
                                <input type="text" name="mobile_subject_len" value="{{ $board['mobile_subject_len'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_subject_len" name="chk_group_mobile_subject_len" value="1" />
                                <label for="chk_group_mobile_subject_len">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_subject_len" name="chk_all_mobile_subject_len" value="1" />
                                <label for="chk_all_mobile_subject_len">전체적용</label>
                            </td>
                        </tr> --}}
                        <tr>
                            <th>페이지당 목록 수</th>
                            <td>
                                <input type="text" name="page_rows" value="{{ $board['page_rows'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_page_rows" name="chk_group_page_rows" value="1" />
                                <label for="chk_group_page_rows">그룹적용</label>
                                <input type="checkbox" id="chk_all_page_rows" name="chk_all_page_rows" value="1" />
                                <label for="chk_all_page_rows">전체적용</label>
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>모바일 페이지당 목록 수</th>
                            <td>
                                <input type="text" name="mobile_page_rows" value="{{ $board['mobile_page_rows'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_page_rows" name="chk_group_mobile_page_rows" value="1" />
                                <label for="chk_group_mobile_page_rows">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_page_rows" name="chk_all_mobile_page_rows" value="1" />
                                <label for="chk_all_mobile_page_rows">전체적용</label>
                            </td>
                        </tr> --}}
                        <tr>
                            <th>갤러리 이미지 수</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 이미지를 한줄에 몇장씩 보여 줄 것인지를 설정하는 값<br />
                                <input type="text" name="gallery_cols" value="{{ $board['gallery_cols'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_gallery_cols" name="chk_group_gallery_cols" value="1" />
                                <label for="chk_group_gallery_cols">그룹적용</label>
                                <input type="checkbox" id="chk_all_gallery_cols" name="chk_all_gallery_cols" value="1" />
                                <label for="chk_all_gallery_cols">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>갤러리 이미지 폭</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 썸네일 이미지의 폭을 설정하는 값<br />
                                <input type="text" name="gallery_width" value="{{ $board['gallery_width'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_gallery_width" name="chk_group_gallery_width" value="1" />
                                <label for="chk_group_gallery_width">그룹적용</label>
                                <input type="checkbox" id="chk_all_gallery_width" name="chk_all_gallery_width" value="1" />
                                <label for="chk_all_gallery_width">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>갤러리 이미지 높이</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 썸네일 이미지의 높이를 설정하는 값<br />
                                <input type="text" name="gallery_height" value="{{ $board['gallery_height'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_gallery_height" name="chk_group_gallery_height" value="1" />
                                <label for="chk_group_gallery_height">그룹적용</label>
                                <input type="checkbox" id="chk_all_gallery_height" name="chk_all_gallery_height" value="1" />
                                <label for="chk_all_gallery_height">전체적용</label>
                            </td>
                        </tr>
                        {{-- <tr>
                            <th>모바일 갤러리 이미지 폭</th>
                            <td>
                                모바일로 접속시 갤러리 형식의 게시판 목록에서 썸네일 이미지의 폭을 설정하는 값<br />
                                <input type="text" name="mobile_gallery_width" value="{{ $board['mobile_gallery_width'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_gallery_width" name="chk_group_mobile_gallery_width" value="1" />
                                <label for="chk_group_mobile_gallery_width">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_gallery_width" name="chk_all_mobile_gallery_width" value="1" />
                                <label for="chk_all_mobile_gallery_width">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 갤러리 이미지 높이</th>
                            <td>
                                모바일로 접속시 갤러리 형식의 게시판 목록에서 썸네일 이미지의 높이를 설정하는 값<br />
                                <input type="text" name="mobile_gallery_height" value="{{ $board['mobile_gallery_height'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_gallery_height" name="chk_group_mobile_gallery_height" value="1" />
                                <label for="chk_group_mobile_gallery_height">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_gallery_height" name="chk_all_mobile_gallery_height" value="1" />
                                <label for="chk_all_mobile_gallery_height">전체적용</label>
                            </td>
                        </tr> --}}
                        <tr>
                            <th>게시판 폭</th>
                            <td>
                                100 이하는 %<br />
                                <input type="text" name="table_width" value="{{ $board['table_width'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_table_width" name="chk_group_table_width" value="1" />
                                <label for="chk_group_table_width">그룹적용</label>
                                <input type="checkbox" id="chk_all_table_width" name="chk_all_table_width" value="1" />
                                <label for="chk_all_table_width">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>이미지 폭 크기</th>
                            <td>
                                게시판에서 출력되는 이미지의 폭 크기<br />
                                <input type="text" name="image_width" value="{{ $board['image_width'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_image_width" name="chk_group_image_width" value="1" />
                                <label for="chk_group_image_width">그룹적용</label>
                                <input type="checkbox" id="chk_all_image_width" name="chk_all_image_width" value="1" />
                                <label for="chk_all_image_width">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>새글 아이콘</th>
                            <td>
                                글 입력후 new 이미지를 출력하는 시간. 0을 입력하시면 아이콘을 출력하지 않습니다.<br />
                                <input type="text" name="new" value="{{ $board['new'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_new" name="chk_group_new" value="1" />
                                <label for="chk_group_new">그룹적용</label>
                                <input type="checkbox" id="chk_all_new" name="chk_all_new" value="1" />
                                <label for="chk_all_new">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>인기글 아이콘</th>
                            <td>
                                조회수가 설정값 이상이면 hot 이미지 출력. 0을 입력하시면 아이콘을 출력하지 않습니다.<br />
                                <input type="text" name="hot" value="{{ $board['hot'] }}" required/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_hot" name="chk_group_hot" value="1" />
                                <label for="chk_group_hot">그룹적용</label>
                                <input type="checkbox" id="chk_all_hot" name="chk_all_hot" value="1" />
                                <label for="chk_all_hot">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>답변 달기</th>
                            <td>
                                <select id="reply_order" name="reply_order">
                                    <option value="1" @if($board['reply_order'] == 1) selected @endif >나중에 쓴 답변 아래로 달기 (기본)
                                    <option value="0" @if($board['reply_order'] == 0) selected @endif>나중에 쓴 답변 위로 달기
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_reply_order" name="chk_group_reply_order" value="1" />
                                <label for="chk_group_reply_order">그룹적용</label>
                                <input type="checkbox" id="chk_all_reply_order" name="chk_all_reply_order" value="1" />
                                <label for="chk_all_reply_order">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>리스트 정렬 필드</th>
                            <td>
                                리스트에서 기본으로 정렬에 사용할 필드를 선택합니다. "기본"으로 사용하지 않으시는 경우 속도가 느려질 수 있습니다.<br />
                                <select id="sort_field" name="sort_field">
                                    <option value="" @if($type == 'create') selected @endif>num, reply : 기본</option>
                                    <option value="created_at asc" @if($type == 'edit' && $board->sort_field == 'created_at asc') selected @endif >created_at asc : 날짜 이전것 부터</option>
                                    <option value="created_at desc" @if($type == 'edit' && $board->sort_field == 'created_at desc') selected @endif >created_at desc : 날짜 최근것 부터</option>
                                    <option value="hit asc, num, reply" @if($type == 'edit' && $board->sort_field == 'hit asc, reply asc') selected @endif >hit asc : 조회수 낮은것 부터</option>
                                    <option value="hit desc, num, reply" @if($type == 'edit' && $board->sort_field == 'hit desc, reply') selected @endif >hit desc : 조회수 높은것 부터</option>
                                    <option value="last asc" @if($type == 'edit' && $board->sort_field == 'last asc') selected @endif >last asc : 최근글 이전것 부터</option>
                                    <option value="last desc" @if($type == 'edit' && $board->sort_field == 'last desc') selected @endif >last desc : 최근글 최근것 부터</option>
                                    <option value="comment asc, num, reply" @if($type == 'edit' && $board->sort_field == 'comment asc') selected @endif >comment asc : 댓글수 낮은것 부터</option>
                                    <option value="comment desc, num, reply" @if($type == 'edit' && $board->sort_field == 'comment desc') selected @endif >comment desc : 댓글수 높은것 부터</option>
                                    <option value="good asc, num, reply" @if($type == 'edit' && $board->sort_field == 'good asc') selected @endif >good asc : 추천수 낮은것 부터</option>
                                    <option value="good desc, num, reply" @if($type == 'edit' && $board->sort_field == 'good desc') selected @endif >good desc : 추천수 높은것 부터</option>
                                    <option value="nogood asc, num, reply" @if($type == 'edit' && $board->sort_field == 'nogood asc') selected @endif >nogood asc : 비추천수 낮은것 부터</option>
                                    <option value="nogood desc, num, reply" @if($type == 'edit' && $board->sort_field == 'nogood desc') selected @endif >nogood desc : 비추천수 높은것 부터</option>
                                    <option value="subject asc, num, reply" @if($type == 'edit' && $board->sort_field == 'subject asc') selected @endif >subject asc : 제목 오름차순</option>
                                    <option value="subject desc, num, reply" @if($type == 'edit' && $board->sort_field == 'subject desc') selected @endif >subject desc : 제목 내림차순</option>
                                    <option value="name asc, num, reply" @if($type == 'edit' && $board->sort_field == 'name asc') selected @endif >name asc : 글쓴이 오름차순</option>
                                    <option value="name desc, num, reply" @if($type == 'edit' && $board->sort_field == 'name desc') selected @endif >name desc : 글쓴이 내림차순</option>
                                    <option value="ca_name asc, num, reply" @if($type == 'edit' && $board->sort_field == 'ca_name asc') selected @endif >ca_name asc : 분류명 오름차순</option>
                                    <option value="ca_name desc, num, reply" @if($type == 'edit' && $board->sort_field == 'ca_name desc') selected @endif >ca_name desc : 분류명 내림차순</option>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_sort_field" name="chk_group_sort_field" value="1" />
                                <label for="chk_group_sort_field">그룹적용</label>
                                <input type="checkbox" id="chk_all_sort_field" name="chk_all_sort_field" value="1" />
                                <label for="chk_all_sort_field">전체적용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                            @if($type == 'edit')
                                <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                    게시판 복사
                                </a>
                                <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                <a class="btn btn-primary" href="">게시판 썸네일 삭제</a>
                            @endif
                        </div>
                    </div>
                </section>
                <section id="anc_point">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 포인트 설정</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                                <a class="btn" href="#anc_extra">여분 필드</a>
                            </p>
                        </tr>
                        <tr>
                            <th>기본값으로 설정</th>
                            <td>
                                환경설정에 입력된 포인트로 설정<br />
                                <input type="checkbox" id="config_env_point"  onclick="set_point(this.form)" />
                            </td>
                        </tr>
                        <tr>
                            <th>글읽기 포인트</th>
                            <td>
                                <input type="text" name="read_point" value='{{ $board['read_point'] }}' required />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_read_point" name="chk_group_read_point" value="1" />
                                <label for="chk_group_read_point">그룹적용</label>
                                <input type="checkbox" id="chk_all_read_point" name="chk_all_read_point" value="1" />
                                <label for="chk_all_read_point">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>글쓰기 포인트</th>
                            <td>
                                <input type="text" name="write_point" value='{{ $board['write_point'] }}' required />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_write_point" name="chk_group_write_point" value="1" />
                                <label for="chk_group_write_point">그룹적용</label>
                                <input type="checkbox" id="chk_all_write_point" name="chk_all_write_point" value="1" />
                                <label for="chk_all_write_point">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>댓글쓰기 포인트</th>
                            <td>
                                <input type="text" name="comment_point" value='{{ $board['comment_point'] }}' required />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_comment_point" name="chk_group_comment_point" value="1" />
                                <label for="chk_group_comment_point">그룹적용</label>
                                <input type="checkbox" id="chk_all_comment_point" name="chk_all_comment_point" value="1" />
                                <label for="chk_all_comment_point">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>다운로드 포인트</th>
                            <td>
                                <input type="text" name="download_point" value='{{ $board['download_point'] }}' required />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_download_point" name="chk_group_download_point" value="1" />
                                <label for="chk_group_download_point">그룹적용</label>
                                <input type="checkbox" id="chk_all_download_point" name="chk_all_download_point" value="1" />
                                <label for="chk_all_download_point">전체적용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                            @if($type == 'edit')
                                <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                    게시판 복사
                                </a>
                                <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                <a class="btn btn-primary" href="">게시판 썸네일 삭제</a>
                            @endif
                        </div>
                    </div>
                </section>
                <section id="anc_extra">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 여분필드 설정</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                                <a class="btn" href="#anc_extra">여분 필드</a>
                            </p>
                        </tr>
                        @for($i=1; $i<=10; $i++)
                            <tr>
                                <th>여분필드 {{ $i }}</th>
                                <td>
                                    여분필드 {{ $i }} 제목 <input type="text" name="subj_{{ $i }}" @if($type == 'edit') value="{{ $board['subj_' .$i] }}" @endif />
                                    여분필드 {{ $i }} 값 <input type="text" name="value_{{ $i }}" @if($type == 'edit') value="{{ $board['value_' .$i] }}" @endif />
                                </td>
                                <td>
                                    <input type="checkbox" id="chk_group_extra_{{ $i }}" name="chk_group_extra_{{ $i }}" value="1" />
                                    <label for="chk_group_{{ $i }}">그룹적용</label>
                                    <input type="checkbox" id="chk_all_extra_{{ $i }}" name="chk_all_extra_{{ $i }}" value="1" />
                                    <label for="chk_all_{{ $i }}">전체적용</label>
                                </td>
                            </tr>
                        @endfor
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                            @if($type == 'edit')
                                <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                    게시판 복사
                                </a>
                                <a class="btn btn-primary" href="{{ route('board.index', $board->id) }}">게시판 바로가기</a>
                                <a class="btn btn-primary" href="">게시판 썸네일 삭제</a>
                            @endif
                        </div>
                    </div>
                </section>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
