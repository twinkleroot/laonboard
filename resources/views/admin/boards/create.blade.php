@extends('theme')

@section('title')
     게시판 생성 | LaBoard
@endsection

@section('content')
<div>
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
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
                                {{-- <a class="btn" href="#anc_design">디자인/양식</a> --}}
                                <a class="btn" href="#anc_point">포인트 설정</a>
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
                                    @foreach ($accessible_groups as $group)
                                        <option value="{{ $group->id }}">
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
                        </tr>
                        <tr>
                            <th>분류</th>
                            <td>
                                분류와 분류 사이는 | 로 구분하세요. (예: 질문|답변) 첫자로 #은 입력하지 마세요. (예: #질문|#답변 [X])<br />
                                <input type="text" name="category_list" />
                                <input type="checkbox" id="use_category" name="use_category" value="1" />
                                <label for="use_category">사용</label>
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
                                {{-- <a class="btn" href="#anc_design">디자인/양식</a> --}}
                                <a class="btn" href="#anc_point">포인트 설정</a>
                            </p>
                        </tr>
                        <tr>
                            <th>게시판 관리자</th>
                            <td>
                                <input type="text" name="admin" />
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
                                {{-- <a class="btn" href="#anc_design">디자인/양식</a> --}}
                                <a class="btn" href="#anc_point">포인트 설정</a>
                            </p>
                        </tr>
                        <tr>
                            <th>원글 수정 불가</th>
                            <td>
                                댓글의 수가 설정 수 이상이면 원글을 수정할 수 없습니다. 0으로 설정하시면 댓글 수에 관계없이 수정할 수있습니다.<br />
                                댓글<input type="text" name="count_modify" value="1" />개 이상 달리면 수정불가
                            </td>
                        </tr>
                        <tr>
                            <th>원글 삭제 불가</th>
                            <td>
                                댓글<input type="text" name="count_delete" value="1" />개 이상 달리면 삭제불가
                            </td>
                        </tr>
                        <tr>
                            <th>글쓴이 사이드뷰</th>
                            <td>
                                <input type="checkbox" id="use_sideview" name="use_sideview" value="1" />
                                <label for="use_sideview">사용(글쓴이 클릭시 나오는 레이어 메뉴)</label>
                            </td>
                        </tr>
                        <tr>
                            <th>비밀글 사용</th>
                            <td>
                                "체크박스"는 글작성시 비밀글 체크가 가능합니다. "무조건"은 작성되는 모든글을 비밀글로 작성합니다. (관리자는 체크박스로 출력합니다.) 스킨에 따라 적용되지 않을 수 있습니다.<br />
                                <select name="use_secret">
                                    <option value='0'>사용하지 않음</option>
                                    <option value='1'>체크박스</option>
                                    <option value='2'>무조건</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>DHTML 에디터 사용</th>
                            <td>
                                글작성시 내용을 DHTML 에디터 기능으로 사용할 것인지 설정합니다. 스킨에 따라 적용되지 않을 수 있습니다.<br />
                                <input type="checkbox" id="use_dhtml_editor" name="use_dhtml_editor" value="1" />
                                <label for="use_dhtml_editor">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>RSS 보이기 사용</th>
                            <td>
                                비회원 글읽기가 가능하고 RSS 보이기 사용에 체크가 되어야만 RSS 지원을 합니다.<br />
                                <input type="checkbox" id="use_rss_view" name="use_rss_view" value="1" />
                                <label for="use_rss_view">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_good" name="use_good" value="1" />
                                <label for="use_good">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>비추천 사용</th>
                            <td>
                                <input type="checkbox" id="use_nogood" name="use_nogood" value="1" />
                                <label for="use_nogood">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>이름(실명) 사용</th>
                            <td>
                                <input type="checkbox" id="use_name" name="use_name" value="1" />
                                <label for="use_name">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>서명보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_signature" name="use_signature" value="1" />
                                <label for="use_signature">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>IP 보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_ip_view" name="use_ip_view" value="1" />
                                <label for="use_ip_view">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록에서 내용 사용</th>
                            <td>
                                목록에서 게시판 제목외에 내용도 읽어와야 할 경우에 설정하는 옵션입니다. 기본은 사용하지 않습니다.<br />
                                <input type="checkbox" id="use_list_content" name="use_list_content" value="1" />
                                <label for="use_list_content">사용(사용시 속도가 느려질 수 있습니다.)</label>
                            </td>
                        </tr>
                        <tr>
                            <th>목록에서 파일 사용</th>
                            <td>
                                목록에서 게시판 첨부파일을 읽어와야 할 경우에 설정하는 옵션입니다. 기본은 사용하지 않습니다.<br />
                                <input type="checkbox" id="use_list_file" name="use_list_file" value="1" />
                                <label for="use_list_file">사용(사용시 속도가 느려질 수 있습니다.)</label>
                            </td>
                        </tr>
                        <tr>
                            <th>전체목록보이기 사용</th>
                            <td>
                                <input type="checkbox" id="use_list_view" name="use_list_view" value="1" />
                                <label for="use_list_view">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>메일발송 사용</th>
                            <td>
                                <input type="checkbox" id="use_email" name="use_email" value="1" />
                                <label for="use_email">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>본인확인 사용</th>
                            <td>
                                본인확인 여부에 따라 게시물을 조회 할 수 있도록 합니다.<br />
                                    <select name="use_cert">
                                        <option value="" selected>사용안함</option>
                                        <option value="cert">본인확인된 회원전체</option>
                                        <option value="adult">본인확인된 성인회원만</option>
                                        <option value="hp-cert">휴대폰 본인확인된 회원전체</option>
                                        <option value="hp-adult">휴대폰 본인확인된 성인회원만</option>
                                        <!-- 환경 설정의 본인확인 설정에 따라서 option이 변경됨. -->
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>파일 업로드 개수</th>
                            <td>
                                게시물 한건당 업로드 할 수 있는 파일의 최대 개수 (0 은 파일첨부 사용하지 않음)<br />
                                <input type="text" name="upload_count" value="2"/>
                            </td>
                        </tr>
                        <tr>
                            <th>파일 업로드 용량</th>
                            <td>
                                최대 1024M 이하 업로드 가능, 1 MB = 1,048,576 bytes<br />
                                업로드 파일 한개당<input type="text" name="upload_size" value="1048576"/>bytes 이하
                            </td>
                        </tr>
                        <tr>
                            <th>파일 설명 사용</th>
                            <td>
                                <input type="checkbox" id="use_file_content" name="use_file_content" value="1" />
                                <label for="use_file_content">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>최소 글수 제한</th>
                            <td>
                                글 입력시 최소 글자수를 설정. 0을 입력하거나 최고관리자, DHTML 에디터 사용시에는 검사하지 않음<br />
                                <input type="text" name="write_min" />
                            </td>
                        </tr>
                        <tr>
                            <th>최대 글수 제한</th>
                            <td>
                                글 입력시 최대 글자수를 설정. 0을 입력하거나 최고관리자, DHTML 에디터 사용시에는 검사하지 않음<br />
                                <input type="text" name="write_max" />
                            </td>
                        </tr>
                        <tr>
                            <th>최소 댓글수 제한</th>
                            <td>
                                댓글 입력시 최소 글자수를 설정. 0을 입력하면 검사하지 않음<br />
                                <input type="text" name="comment_min" />
                            </td>
                        </tr>
                        <tr>
                            <th>최대 댓글수 제한</th>
                            <td>
                                댓글 입력시 최대 글자수를 설정. 0을 입력하면 검사하지 않음<br />
                                <input type="text" name="comment_max" />
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
                        </tr>
                        <tr>
                            <th>전체 검색 사용</th>
                            <td>
                                <input type="checkbox" id="use_search" name="use_search" value="1" checked/>
                                <label for="use_search">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>출력 순서</th>
                            <td>
                                숫자가 낮은 게시판 부터 메뉴나 검색시 우선 출력합니다.<br />
                                <input type="text" id="order" name="order" />
                            </td>
                        </tr>
                    </table>
                </section>
                {{-- <section id="anc_design">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 디자인/양식</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                <a class="btn" href="#anc_design">디자인/양식</a>
                                <a class="btn" href="#anc_point">포인트 설정</a>
                            </p>
                        </tr>

                    </table>
                </section> --}}
                <section id="anc_point">
                    <table class="table table-hover">
                        <tr>
                            <p>
                                <h4>게시판 포인트 설정</h4>
                                <a class="btn" href="#anc_basic">기본 설정</a>
                                <a class="btn" href="#anc_auth">권한 설정</a>
                                <a class="btn" href="#anc_function">기능 설정</a>
                                {{-- <a class="btn" href="#anc_design">디자인/양식</a> --}}
                                <a class="btn" href="#anc_point">포인트 설정</a>
                            </p>
                        </tr>
                        <tr>
                            <th>기본값으로 설정</th>
                            <td>
                                환경설정에 입력된 포인트로 설정<br />
                                <input type="checkbox" id="chk_default_point" value="1" />
                            </td>
                        </tr>
                        <tr>
                            <th>글읽기 포인트</th>
                            <td>
                                <input type="text" name="read_point" value='0' />
                            </td>
                        </tr>
                        <tr>
                            <th>글쓰기 포인트</th>
                            <td>
                                <input type="text" name="write_point" value='0' />
                            </td>
                        </tr>
                        <tr>
                            <th>댓글쓰기 포인트</th>
                            <td>
                                <input type="text" name="comment_point" value='0' />
                            </td>
                        </tr>
                        <tr>
                            <th>다운로드 포인트</th>
                            <td>
                                <input type="text" name="download_point" value='0' />
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
@endsection
