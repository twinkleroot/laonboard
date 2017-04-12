@extends('theme')

@section('title')
    게시판 수정 | LaBoard
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">게시판 수정</div>
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.boards.update', $board->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
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
                            <td @if($errors->get('table')) class="has-error" @endif>
                                <input type="text" name="table" value="{{ $board->table }}" maxlength="20" readonly />
                                영문자, 숫자, _ 만 가능(공백없이 20자 이내)
                                @foreach ($errors->get('table') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                                <a class="btn btn-primary" href="">게시판바로가기</a>
                                <a class="btn btn-primary" href="{{ route('admin.boards.index')}}">목록으로</a>
                            </td>
                        </tr>
                        <tr>
                            <th>그룹</th>
                            <td>
                                <select name="group_id" required>
                                    <option value>선택</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" @if($group->id == $board->group_id) selected @endif>
                                            {{ $group->subject }}
                                        </option>
                                    @endforeach
                                </select>
                                <a class="btn btn-primary" href="/admin/search?admin_page=board&kind=group_id&keyword={{ $keyword }}">
                                    동일그룹 게시판목록
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>게시판 제목</th>
                            <td @if($errors->get('subject')) class="has-error" @endif>
                                <input type="text" name="subject" value="{{ $board->subject }}" required/>
                                @foreach ($errors->get('subject') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 게시판 제목</th>
                            <td>
                                모바일에서 보여지는 게시판 제목이 다른 경우에 입력합니다. 입력이 없으면 기본 게시판 제목이 출력됩니다.<br />
                                <input type="text" name="mobile_subject" value="{{ $board->mobile_subject }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>접속기기</th>
                            <td>
                                PC 와 모바일 사용을 구분합니다.<br />
                                <select name="device">
                                    <option value="both" @if($board->device == 'both') selected @endif>PC와 모바일에서 모두 사용</option>
                                    <option value="pc" @if($board->device == 'pc') selected @endif>PC 전용</option>
                                    <option value="mobile" @if($board->device == 'mobile') selected @endif>모바일 전용</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>분류</th>
                            <td>
                                분류와 분류 사이는 | 로 구분하세요. (예: 질문|답변) 첫자로 #은 입력하지 마세요. (예: #질문|#답변 [X])<br />
                                <input type="text" name="category_list" value="{{ $board->category_list }}" />
                                <input type="checkbox" id="use_category" name="use_category" value="1"
                                    @if($board->use_category == '1') checked @endif />
                                <label for="use_category">사용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                복사
                            </a>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
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
                                <input type="text" name="admin" value="{{ $board->admin }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>목록보기 권한</th>
                            <td>
                                <select name="list_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->list_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>글읽기 권한</th>
                            <td>
                                <select name="read_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->read_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>글쓰기 권한</th>
                            <td>
                                <select name="write_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->write_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>글답변 권한</th>
                            <td>
                                <select name="reply_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->reply_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>댓글쓰기 권한</th>
                            <td>
                                <select name="comment_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->comment_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>링크 권한</th>
                            <td>
                                <select name="link_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->link_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>업로드 권한</th>
                            <td>
                                <select name="upload_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->upload_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>다운로드 권한</th>
                            <td>
                                <select name="download_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->download_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>HTML 쓰기 권한</th>
                            <td>
                                <select name="html_level">
                                    @for($i=1; $i<=10; $i++)
                                        <option value="{{ $i }}" @if($board->html_level == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                복사
                            </a>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                        </div>
                    </div>
                </section>
                <section id="anc_function">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 기능설정</h4>
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
                                댓글<input type="text" name="count_modify" value="{{ $board->count_modify }}" />개 이상 달리면 수정불가
                            </td>
                        </tr>
                        <tr>
                            <th>원글 삭제 불가</th>
                            <td>
                                댓글<input type="text" name="count_delete" value="{{ $board->count_delete }}" />개 이상 달리면 삭제불가
                            </td>
                        </tr>
                        <tr>
                            <th>글쓴이 사이드뷰</th>
                            <td>
                                <input type="checkbox" id="use_sideview" name="use_sideview" value="1"
                                    @if($board->use_sideview == '1') checked @endif />
                                <label for="use_sideview">사용(글쓴이 클릭시 나오는 레이어 메뉴)</label>
                            </td>
                        </tr>
                        <tr>
                            <th>비밀글 사용</th>
                            <td>
                                "체크박스"는 글작성시 비밀글 체크가 가능합니다. "무조건"은 작성되는 모든글을 비밀글로 작성합니다. (관리자는 체크박스로 출력합니다.) 스킨에 따라 적용되지 않을 수 있습니다.<br />
                                <select name="use_secret">
                                    <option value='0' @if($board->use_secret == '0') selected @endif>사용하지 않음</option>
                                    <option value='1' @if($board->use_secret == '1') selected @endif>체크박스</option>
                                    <option value='2' @if($board->use_secret == '2') selected @endif>무조건</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>DHTML 에디터 사용</th>
                            <td>
                                글작성시 내용을 DHTML 에디터 기능으로 사용할 것인지 설정합니다. 스킨에 따라 적용되지 않을 수 있습니다.<br />
                                <input type="checkbox" id="use_dhtml_editor" name="use_dhtml_editor" value="1"
                                    @if($board->use_dhtml_editor == '1') checked @endif />
                                <label for="use_dhtml_editor">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>RSS 보이기 사용</th>
                            <td>
                                비회원 글읽기가 가능하고 RSS 보이기 사용에 체크가 되어야만 RSS 지원을 합니다.<br />
                                <input type="checkbox" id="use_rss_view" name="use_rss_view" value="1"
                                    @if($board->use_rss_view == '1') checked @endif />
                                <label for="use_rss_view">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_good" name="use_good" value="1"
                                    @if($board->use_good == '1') checked @endif />
                                <label for="use_good">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>비추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_nogood" name="use_nogood" value="1"
                                    @if($board->use_nogood == '1') checked @endif />
                                <label for="use_nogood">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>이름(실명) 사용</th>
                            <td>
                                <input type="checkbox" id="use_name" name="use_name" value="1"
                                    @if($board->use_name == '1') checked @endif />
                                <label for="use_name">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>서명보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_signature" name="use_signature" value="1"
                                    @if($board->use_signature == '1') checked @endif />
                                <label for="use_signature">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>IP 보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_ip_view" name="use_ip_view" value="1"
                                    @if($board->use_ip_view == '1') checked @endif />
                                <label for="use_ip_view">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록에서 내용 사용</th>
                            <td>
                                목록에서 게시판 제목외에 내용도 읽어와야 할 경우에 설정하는 옵션입니다. 기본은 사용하지 않습니다.<br />
                                <input type="checkbox" id="use_list_content" name="use_list_content" value="1"
                                    @if($board->use_list_content == '1') checked @endif />
                                <label for="use_list_content">사용(사용시 속도가 느려질 수 있습니다.)</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록에서 파일 사용</th>
                            <td>
                                목록에서 게시판 첨부파일을 읽어와야 할 경우에 설정하는 옵션입니다. 기본은 사용하지 않습니다.<br />
                                <input type="checkbox" id="use_list_file" name="use_list_file" value="1"
                                    @if($board->use_list_file == '1') checked @endif />
                                <label for="use_list_file">사용(사용시 속도가 느려질 수 있습니다.)</label>
                            </td>
                        </tr>
                        <tr>
                            <th>전체목록보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_list_view" name="use_list_view" value="1"
                                    @if($board->use_list_view == '1') checked @endif />
                                <label for="use_list_view">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>메일발송 사용</th>
                            <td>
                                <input type="checkbox" id="use_email" name="use_email" value="1"
                                    @if($board->use_email == '1') checked @endif />
                                <label for="use_email">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>본인확인 사용</th>
                            <td>
                                본인확인 여부에 따라 게시물을 조회 할 수 있도록 합니다.<br />
                                    <select name="use_cert">
                                        <option value="" @if($board->use_cert == "") selected @endif>사용안함</option>
                                        <option value="cert" @if($board->use_cert == "cert") selected @endif>본인확인된 회원전체</option>
                                        <option value="adult" @if($board->use_cert == "adult") selected @endif>본인확인된 성인회원만</option>
                                        <option value="hp-cert" @if($board->use_cert == "hp-cert") selected @endif>휴대폰 본인확인된 회원전체</option>
                                        <option value="hp-adult" @if($board->use_cert == "hp-adult") selected @endif>휴대폰 본인확인된 성인회원만</option>
                                        <!-- 환경 설정의 본인확인 설정에 따라서 option이 변경됨. -->
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>파일 업로드 개수</th>
                            <td>
                                게시물 한건당 업로드 할 수 있는 파일의 최대 개수 (0 은 파일첨부 사용하지 않음)<br />
                                <input type="text" name="upload_count" value="{{ $board->upload_count }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>파일 업로드 용량</th>
                            <td>
                                최대 1024M 이하 업로드 가능, 1 MB = 1,048,576 bytes<br />
                                업로드 파일 한개당<input type="text" name="upload_size" value="{{ $board->upload_size }}" />bytes 이하
                            </td>
                        </tr>
                        <tr>
                            <th>파일 설명 사용</th>
                            <td>
                                <input type="checkbox" id="use_file_content" name="use_file_content" value="1"
                                    @if($board->use_file_content == '1') checked @endif />
                                <label for="use_file_content">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>최소 글수 제한</th>
                            <td>
                                글 입력시 최소 글자수를 설정. 0을 입력하거나 최고관리자, DHTML 에디터 사용시에는 검사하지 않음<br />
                                <input type="text" name="write_min" value="{{ $board->write_min }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>최대 글수 제한</th>
                            <td>
                                글 입력시 최대 글자수를 설정. 0을 입력하거나 최고관리자, DHTML 에디터 사용시에는 검사하지 않음<br />
                                <input type="text" name="write_max" value="{{ $board->write_max }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>최소 댓글수 제한</th>
                            <td>
                                댓글 입력시 최소 글자수를 설정. 0을 입력하면 검사하지 않음<br />
                                <input type="text" name="comment_min" value="{{ $board->comment_min }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>최대 댓글수 제한</th>
                            <td>
                                댓글 입력시 최대 글자수를 설정. 0을 입력하면 검사하지 않음<br />
                                <input type="text" name="comment_max" value="{{ $board->comment_max }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>SNS 사용</th>
                            <td>
                                사용에 체크하시면 소셜네트워크서비스(SNS)에 글을 퍼가거나 댓글을 동시에 등록할수 있습니다.<br />
                                기본환경설정의 SNS 설정을 하셔야 사용이 가능합니다.<br />
                                <input type="checkbox" id="use_sns" name="use_sns" value="1"
                                    @if($board->use_sns == '1') checked @endif />
                                <label for="use_sns">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>전체 검색 사용</th>
                            <td>
                                <input type="checkbox" id="use_search" name="use_search" value="1"
                                    @if($board->use_search == '1') checked @endif />
                                <label for="use_search">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>출력 순서</th>
                            <td>
                                숫자가 낮은 게시판 부터 메뉴나 검색시 우선 출력합니다.<br />
                                <input type="text" id="order" name="order" value="{{ $board->order }}" />
                            </td>
                        </tr>
                    </table>
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
                            <th>제목 길이</th>
                            <td>
                                목록에서의 제목 글자수. 잘리는 글은 … 로 표시<br />
                                <input type="text" name="subject_len" value="{{ $board->subject_len }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 제목 길이</th>
                            <td>
                                목록에서의 제목 글자수. 잘리는 글은 … 로 표시<br />
                                <input type="text" name="mobile_subject_len" value="{{ $board->mobile_subject_len }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>페이지당 목록 수</th>
                            <td>
                                <input type="text" name="page_rows" value="{{ $board->page_rows }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 페이지당 목록 수</th>
                            <td>
                                <input type="text" name="mobile_page_rows" value="{{ $board->mobile_page_rows }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>갤러리 이미지 수</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 이미지를 한줄에 몇장씩 보여 줄 것인지를 설정하는 값<br />
                                <input type="text" name="gallery_cols" value="{{ $board->gallery_cols }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>갤러리 이미지 폭</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 썸네일 이미지의 폭을 설정하는 값<br />
                                <input type="text" name="gallery_width" value="{{ $board->gallery_width }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>갤러리 이미지 높이</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 썸네일 이미지의 높이를 설정하는 값<br />
                                <input type="text" name="gallery_height" value="{{ $board->gallery_height }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 갤러리 이미지 폭</th>
                            <td>
                                모바일로 접속시 갤러리 형식의 게시판 목록에서 썸네일 이미지의 폭을 설정하는 값<br />
                                <input type="text" name="mobile_gallery_width" value="{{ $board->mobile_gallery_width }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 갤러리 이미지 높이</th>
                            <td>
                                모바일로 접속시 갤러리 형식의 게시판 목록에서 썸네일 이미지의 높이를 설정하는 값<br />
                                <input type="text" name="mobile_gallery_height" value="{{ $board->mobile_gallery_height }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>게시판 폭</th>
                            <td>
                                100 이하는 %<br />
                                <input type="text" name="table_width" value="{{ $board->table_width }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>이미지 폭 크기</th>
                            <td>
                                게시판에서 출력되는 이미지의 폭 크기<br />
                                <input type="text" name="image_width" value="{{ $board->image_width }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>새글 아이콘</th>
                            <td>
                                글 입력후 new 이미지를 출력하는 시간. 0을 입력하시면 아이콘을 출력하지 않습니다.<br />
                                <input type="text" name="new" value="{{ $board->new }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>인기글 아이콘</th>
                            <td>
                                조회수가 설정값 이상이면 hot 이미지 출력. 0을 입력하시면 아이콘을 출력하지 않습니다.<br />
                                <input type="text" name="hot" value="{{ $board->hot }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>답변 달기</th>
                            <td>
                                <select id="reply_order" name="reply_order">
                                    <option value="1" selected="selected">나중에 쓴 답변 아래로 달기 (기본)
                                    <option value="0">나중에 쓴 답변 위로 달기
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>리스트 정렬 필드</th>
                            <td>
                                리스트에서 기본으로 정렬에 사용할 필드를 선택합니다. "기본"으로 사용하지 않으시는 경우 속도가 느려질 수 있습니다.<br />
                                <select id="sort_field" name="sort_field">
                                    <option value=""  selected="selected">reply : 기본</option>
                                    <option value="created_at asc" >created_at asc : 날짜 이전것 부터</option>
                                    <option value="created_at desc" >created_at desc : 날짜 최근것 부터</option>
                                    <option value="hit asc, wr_reply" >hit asc : 조회수 낮은것 부터</option>
                                    <option value="hit desc, wr_reply" >hit desc : 조회수 높은것 부터</option>
                                    <option value="last asc" >last asc : 최근글 이전것 부터</option>
                                    <option value="last desc" >last desc : 최근글 최근것 부터</option>
                                    <option value="comment asc, reply" >comment asc : 댓글수 낮은것 부터</option>
                                    <option value="comment desc, reply" >comment desc : 댓글수 높은것 부터</option>
                                    <option value="good asc, reply" >good asc : 추천수 낮은것 부터</option>
                                    <option value="good desc, reply" >good desc : 추천수 높은것 부터</option>
                                    <option value="nogood asc, reply" >nogood asc : 비추천수 낮은것 부터</option>
                                    <option value="nogood desc, reply" >nogood desc : 비추천수 높은것 부터</option>
                                    <option value="subject asc, reply" >subject asc : 제목 오름차순</option>
                                    <option value="subject desc, reply" >subject desc : 제목 내림차순</option>
                                    <option value="name asc, reply" >name asc : 글쓴이 오름차순</option>
                                    <option value="name desc, reply" >name desc : 글쓴이 내림차순</option>
                                    <option value="ca_name asc, reply" >ca_name asc : 분류명 오름차순</option>
                                    <option value="ca_name desc, reply" >ca_name desc : 분류명 내림차순</option>
                                </select>
                            </td>
                        </tr>
                    </table>
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
                                <input type="checkbox" id="config_env_point" onclick="set_point(this.form)" />
                            </td>
                        </tr>
                        <tr>
                            <th>글읽기 포인트</th>
                            <td>
                                <input type="text" name="read_point" value="{{ $board->read_point }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>글쓰기 포인트</th>
                            <td>
                                <input type="text" name="write_point" value="{{ $board->write_point }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>댓글쓰기 포인트</th>
                            <td>
                                <input type="text" name="comment_point" value="{{ $board->comment_point }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>다운로드 포인트</th>
                            <td>
                                <input type="text" name="download_point" value="{{ $board->download_point }}" />
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                복사
                            </a>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
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
                        <tr>
                            <th>여분필드1</th>
                            <td>
                                여분필드 1 제목 <input type="text" name="subj_1" value="{{ $board->subj_1 }}" />
                                여분필드 1 값 <input type="text" name="value_1" value="{{ $board->value_1 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드2</th>
                            <td>
                                여분필드 2 제목 <input type="text" name="subj_2" value="{{ $board->subj_2 }}" />
                                여분필드 2 값 <input type="text" name="value_2" value="{{ $board->value_2 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드3</th>
                            <td>
                                여분필드 3 제목 <input type="text" name="subj_3" value="{{ $board->subj_3 }}" />
                                여분필드 3 값 <input type="text" name="value_3" value="{{ $board->value_3 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드4</th>
                            <td>
                                여분필드 4 제목 <input type="text" name="subj_4" value="{{ $board->subj_4 }}" />
                                여분필드 4 값 <input type="text" name="value_4" value="{{ $board->value_4 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드5</th>
                            <td>
                                여분필드 5 제목 <input type="text" name="subj_5" value="{{ $board->subj_5 }}" />
                                여분필드 5 값 <input type="text" name="value_5" value="{{ $board->value_5 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드6</th>
                            <td>
                                여분필드 6 제목 <input type="text" name="subj_6" value="{{ $board->subj_6 }}" />
                                여분필드 6 값 <input type="text" name="value_6" value="{{ $board->value_6 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드7</th>
                            <td>
                                여분필드 7 제목 <input type="text" name="subj_7" value="{{ $board->subj_7 }}" />
                                여분필드 7 값 <input type="text" name="value_7" value="{{ $board->value_7 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드8</th>
                            <td>
                                여분필드 8 제목 <input type="text" name="subj_8" value="{{ $board->subj_8 }}" />
                                여분필드 8 값 <input type="text" name="value_8" value="{{ $board->value_8 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드9</th>
                            <td>
                                여분필드 9 제목 <input type="text" name="subj_9" value="{{ $board->subj_9 }}" />
                                여분필드 9 값 <input type="text" name="value_9" value="{{ $board->value_9 }}" />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드10</th>
                            <td>
                                여분필드 10 제목 <input type="text" name="subj_10" value="{{ $board->subj_10 }}" />
                                여분필드 10 값 <input type="text" name="value_10" value="{{ $board->value_10 }}" />
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a href="{{ route('admin.boards.copyForm', $board->id) }}" class="btn btn-primary board_copy" target="win_board_copy">
                                복사
                            </a>
                            <a class="btn btn-primary" href="{{ route('admin.boards.index') }}">목록</a>
                        </div>
                    </div>
                </section>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
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
        f.read_point.value = {{ App\Config::getConfig('config.board')->readPoint }};
        f.write_point.value = {{ App\Config::getConfig('config.board')->writePoint }};
        f.comment_point.value = {{ App\Config::getConfig('config.board')->commentPoint }};
        f.download_point.value = {{ App\Config::getConfig('config.board')->downloadPoint }};
    } else {
        f.read_point.value     = f.read_point.defaultValue;
        f.write_point.value    = f.write_point.defaultValue;
        f.comment_point.value  = f.comment_point.defaultValue;
        f.download_point.value = f.download_point.defaultValue;
    }
}
</script>
@endsection
