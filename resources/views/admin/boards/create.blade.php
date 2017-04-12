@extends('theme')

@section('title')
     게시판 생성 | LaBoard
@endsection

@section('content')
<div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>게시판 생성</h2></div>
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.boards.store') }}">
                    {{ csrf_field() }}
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
                                <input type="text" name="table" required/>
                                영문자, 숫자, _ 만 가능(공백없이 20자 이내)
                                @foreach ($errors->get('table') as $message)
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
                                        <option value="{{ $group->id }}" @if($group->id == $selectedGroup) selected @endif>
                                            {{ $group->subject }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>게시판 제목</th>
                            <td @if($errors->get('subject')) class="has-error" @endif>
                                <input type="text" name="subject" required/>
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
                                <input type="text" name="mobile_subject" />
                            </td>
                        </tr>
                        <tr>
                            <th>접속기기</th>
                            <td>
                                PC 와 모바일 사용을 구분합니다.<br />
                                <select name="device">
                                    <option value="both" selected>PC와 모바일에서 모두 사용</option>
                                    <option value="pc">PC 전용</option>
                                    <option value="mobile">모바일 전용</option>
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
                                <input type="text" name="category_list" />
                                <input type="checkbox" id="use_category" name="use_category" value="1" />
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
                                <input type="text" name="admin" />
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                        <option value="{{ $i }}">
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
                                댓글<input type="text" name="count_modify" value="{{ $board['count_modify'] }}" />개 이상 달리면 수정불가
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
                                댓글<input type="text" name="count_delete" value="{{ $board['count_delete'] }}" />개 이상 달리면 삭제불가
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
                                <input type="checkbox" id="use_sideview" name="use_sideview" value="1" />
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
                                <input type="checkbox" id="use_dhtml_editor" name="use_dhtml_editor" value="1" />
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
                                <input type="checkbox" id="use_rss_view" name="use_rss_view" value="1" />
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
                            <th>추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_good" name="use_good" value="1" />
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
                                <input type="checkbox" id="use_nogood" name="use_nogood" value="1" />
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
                                <input type="checkbox" id="use_name" name="use_name" value="1" />
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
                                <input type="checkbox" id="use_signature" name="use_signature" value="1" />
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
                                <input type="checkbox" id="use_ip_view" name="use_ip_view" value="1" />
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
                                <input type="checkbox" id="use_list_content" name="use_list_content" value="1" />
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
                                <input type="checkbox" id="use_list_file" name="use_list_file" value="1" />
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
                                <input type="checkbox" id="use_list_view" name="use_list_view" value="1" />
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
                                <input type="checkbox" id="use_email" name="use_email" value="1" />
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
                                        <option value="" selected>사용안함</option>
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
                                <input type="text" name="upload_count" value="{{ $board['upload_count'] }}"/>
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
                                업로드 파일 한개당<input type="text" name="upload_size" value="{{ $board['upload_size'] }}"/>bytes 이하
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
                                <input type="checkbox" id="use_file_content" name="use_file_content" value="1" />
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
                                <input type="text" name="write_min" />
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
                                <input type="text" name="write_max" />
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
                                <input type="text" name="comment_min" />
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
                                <input type="text" name="comment_max" />
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
                                <input type="checkbox" id="use_sns" name="use_sns" value="1" />
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
                                <input type="text" id="order" name="order" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_order" name="chk_group_order" value="1" />
                                <label for="chk_group_order">그룹적용</label>
                                <input type="checkbox" id="chk_all_order" name="chk_all_order" value="1" />
                                <label for="chk_all_order">전체적용</label>
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
                                <input type="text" name="subject_len" value="{{ $board['subject_len'] }}"/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_subject_len" name="chk_group_subject_len" value="1" />
                                <label for="chk_group_subject_len">그룹적용</label>
                                <input type="checkbox" id="chk_all_subject_len" name="chk_all_subject_len" value="1" />
                                <label for="chk_all_subject_len">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 제목 길이</th>
                            <td>
                                목록에서의 제목 글자수. 잘리는 글은 … 로 표시<br />
                                <input type="text" name="mobile_subject_len" value="{{ $board['mobile_subject_len'] }}"/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_subject_len" name="chk_group_mobile_subject_len" value="1" />
                                <label for="chk_group_mobile_subject_len">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_subject_len" name="chk_all_mobile_subject_len" value="1" />
                                <label for="chk_all_mobile_subject_len">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>페이지당 목록 수</th>
                            <td>
                                <input type="text" name="page_rows" value="{{ $board['page_rows'] }}"/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_page_rows" name="chk_group_page_rows" value="1" />
                                <label for="chk_group_page_rows">그룹적용</label>
                                <input type="checkbox" id="chk_all_page_rows" name="chk_all_page_rows" value="1" />
                                <label for="chk_all_page_rows">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 페이지당 목록 수</th>
                            <td>
                                <input type="text" name="mobile_page_rows" value="{{ $board['mobile_page_rows'] }}"/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_page_rows" name="chk_group_mobile_page_rows" value="1" />
                                <label for="chk_group_mobile_page_rows">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_page_rows" name="chk_all_mobile_page_rows" value="1" />
                                <label for="chk_all_mobile_page_rows">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>갤러리 이미지 수</th>
                            <td>
                                갤러리 형식의 게시판 목록에서 이미지를 한줄에 몇장씩 보여 줄 것인지를 설정하는 값<br />
                                <input type="text" name="gallery_cols" value="{{ $board['gallery_cols'] }}"/>
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
                                <input type="text" name="gallery_width" value="{{ $board['gallery_width'] }}"/>
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
                                <input type="text" name="gallery_height" value="{{ $board['gallery_height'] }}"/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_gallery_height" name="chk_group_gallery_height" value="1" />
                                <label for="chk_group_gallery_height">그룹적용</label>
                                <input type="checkbox" id="chk_all_gallery_height" name="chk_all_gallery_height" value="1" />
                                <label for="chk_all_gallery_height">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>모바일 갤러리 이미지 폭</th>
                            <td>
                                모바일로 접속시 갤러리 형식의 게시판 목록에서 썸네일 이미지의 폭을 설정하는 값<br />
                                <input type="text" name="mobile_gallery_width" value="{{ $board['mobile_gallery_width'] }}"/>
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
                                <input type="text" name="mobile_gallery_height" value="{{ $board['mobile_gallery_height'] }}"/>
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_mobile_gallery_height" name="chk_group_mobile_gallery_height" value="1" />
                                <label for="chk_group_mobile_gallery_height">그룹적용</label>
                                <input type="checkbox" id="chk_all_mobile_gallery_height" name="chk_all_mobile_gallery_height" value="1" />
                                <label for="chk_all_mobile_gallery_height">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>게시판 폭</th>
                            <td>
                                100 이하는 %<br />
                                <input type="text" name="table_width" value="{{ $board['table_width'] }}"/>
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
                                <input type="text" name="image_width" value="{{ $board['image_width'] }}"/>
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
                                <input type="text" name="new" value="{{ $board['new'] }}"/>
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
                                <input type="text" name="hot" value="{{ $board['hot'] }}"/>
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
                                    <option value=""  selected="selected">reply : 기본</option>
                                    <option value="created_at asc" >created_at asc : 날짜 이전것 부터</option>
                                    <option value="created_at desc" >created_at desc : 날짜 최근것 부터</option>
                                    <option value="hit asc, reply" >hit asc : 조회수 낮은것 부터</option>
                                    <option value="hit desc, reply" >hit desc : 조회수 높은것 부터</option>
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
                            <td>
                                <input type="checkbox" id="chk_group_sort_field" name="chk_group_sort_field" value="1" />
                                <label for="chk_group_sort_field">그룹적용</label>
                                <input type="checkbox" id="chk_all_sort_field" name="chk_all_sort_field" value="1" />
                                <label for="chk_all_sort_field">전체적용</label>
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
                                <input type="checkbox" id="config_env_point"  onclick="set_point(this.form)" />
                            </td>
                        </tr>
                        <tr>
                            <th>글읽기 포인트</th>
                            <td>
                                <input type="text" name="read_point" value='{{ $board['read_point'] }}' />
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
                                <input type="text" name="write_point" value='{{ $board['write_point'] }}' />
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
                                <input type="text" name="comment_point" value='{{ $board['comment_point'] }}' />
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
                                <input type="text" name="download_point" value='{{ $board['download_point'] }}' />
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
                                여분필드 1 제목 <input type="text" name="subj_1" />
                                여분필드 1 값 <input type="text" name="value_1" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_1" name="chk_group_1" value="1" />
                                <label for="chk_group_1">그룹적용</label>
                                <input type="checkbox" id="chk_all_1" name="chk_all_1" value="1" />
                                <label for="chk_all_1">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드2</th>
                            <td>
                                여분필드 2 제목 <input type="text" name="subj_2" />
                                여분필드 2 값 <input type="text" name="value_2" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_2" name="chk_group_2" value="1" />
                                <label for="chk_group_2">그룹적용</label>
                                <input type="checkbox" id="chk_all_2" name="chk_all_2" value="1" />
                                <label for="chk_all_2">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드3</th>
                            <td>
                                여분필드 3 제목 <input type="text" name="subj_3" />
                                여분필드 3 값 <input type="text" name="value_3" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_3" name="chk_group_3" value="1" />
                                <label for="chk_group_3">그룹적용</label>
                                <input type="checkbox" id="chk_all_3" name="chk_all_3" value="1" />
                                <label for="chk_all_3">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드4</th>
                            <td>
                                여분필드 4 제목 <input type="text" name="subj_4" />
                                여분필드 4 값 <input type="text" name="value_4" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_4" name="chk_group_4" value="1" />
                                <label for="chk_group_4">그룹적용</label>
                                <input type="checkbox" id="chk_all_4" name="chk_all_4" value="1" />
                                <label for="chk_all_4">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드5</th>
                            <td>
                                여분필드 5 제목 <input type="text" name="subj_5" />
                                여분필드 5 값 <input type="text" name="value_5" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_5" name="chk_group_5" value="1" />
                                <label for="chk_group_5">그룹적용</label>
                                <input type="checkbox" id="chk_all_5" name="chk_all_5" value="1" />
                                <label for="chk_all_5">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드6</th>
                            <td>
                                여분필드 6 제목 <input type="text" name="subj_6" />
                                여분필드 6 값 <input type="text" name="value_6" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_6" name="chk_group_6" value="1" />
                                <label for="chk_group_6">그룹적용</label>
                                <input type="checkbox" id="chk_all_6" name="chk_all_6" value="1" />
                                <label for="chk_all_6">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드7</th>
                            <td>
                                여분필드 7 제목 <input type="text" name="subj_7" />
                                여분필드 7 값 <input type="text" name="value_7" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_7" name="chk_group_7" value="1" />
                                <label for="chk_group_7">그룹적용</label>
                                <input type="checkbox" id="chk_all_7" name="chk_all_7" value="1" />
                                <label for="chk_all_7">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드8</th>
                            <td>
                                여분필드 8 제목 <input type="text" name="subj_8" />
                                여분필드 8 값 <input type="text" name="value_8" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_8" name="chk_group_8" value="1" />
                                <label for="chk_group_8">그룹적용</label>
                                <input type="checkbox" id="chk_all_8" name="chk_all_8" value="1" />
                                <label for="chk_all_8">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드9</th>
                            <td>
                                여분필드 9 제목 <input type="text" name="subj_9" />
                                여분필드 9 값 <input type="text" name="value_9" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_9" name="chk_group_9" value="1" />
                                <label for="chk_group_9">그룹적용</label>
                                <input type="checkbox" id="chk_all_9" name="chk_all_9" value="1" />
                                <label for="chk_all_9">전체적용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드10</th>
                            <td>
                                여분필드 10 제목 <input type="text" name="subj_10" />
                                여분필드 10 값 <input type="text" name="value_10" />
                            </td>
                            <td>
                                <input type="checkbox" id="chk_group_10" name="chk_group_10" value="1" />
                                <label for="chk_group_10">그룹적용</label>
                                <input type="checkbox" id="chk_all_10" name="chk_all_10" value="1" />
                                <label for="chk_all_10">전체적용</label>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-5">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
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
// 환경설정에 입력된 포인트로 설정 함수
function set_point(f) {
    if (f.config_env_point.checked) {
        f.read_point.value = {{ $board['read_point'] }};
        f.write_point.value = {{ $board['write_point'] }};
        f.comment_point.value = {{ $board['comment_point'] }};
        f.download_point.value = {{ $board['download_point'] }};
    } else {
        f.read_point.value     = f.read_point.defaultValue;
        f.write_point.value    = f.write_point.defaultValue;
        f.comment_point.value  = f.comment_point.defaultValue;
        f.download_point.value = f.download_point.defaultValue;
    }
}
</script>
@endsection
