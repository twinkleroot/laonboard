@extends('install.layout')

@section('title')
    {{ config('app.name')." 초기환경설정 2/3" }}
@endsection

@section('step')
    INSTALLATION
@endsection

@section('content')
<div class="container">
    <ul class="step">
        <li>1. 라이센스 확인</li>
        <li class="on">2. 초기환경설정</li>
        <li>3. 설치 완료</li>
    </ul>
    @if($agree != '동의함')
    <div class="ins_inner">
        <p>라이센스(License) 내용에 동의하셔야 설치를 계속하실 수 있습니다.</p>
        <div class="inner_btn">
            <a onclick="history.back();" class="btn">뒤로가기</a>
        </div>
    </div>
    @else
    <form action="{{ route('install.setup') }}" id="frm_install" method="post" autocomplete="off" onsubmit="return frm_install_submit(this);">
        <div class="ins_inner">
            <table class="ins_frm">
                <caption>App 정보입력</caption>
                <tbody>
                    <tr>
                        <th><label for="appUrl">App Url</label></th>
                        <td>
                            <input name="appUrl" type="text" class="form-control" value="{{ env('APP_URL', Request::root()) }}" id="appUrl">
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="ins_frm">
                <caption>MySQL 정보입력</caption>
                <tbody>
                    <tr>
                        <th scope="row"><label for="mysqlHost">Host</label></th>
                        <td>
                            <input name="mysqlHost" type="text" class="form-control" value="{{ env('DB_HOST', '127.0.0.1') }}" id="mysqlHost">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlPort">Port</label></th>
                        <td>
                            <input name="mysqlPort" type="text" class="form-control" value="{{ env('DB_PORT', '3306') }}" id="mysqlPort">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlDb">Database</label></th>
                        <td>
                            <input name="mysqlDb" type="text" class="form-control" id="mysqlDb" value="{{ env('DB_DATABASE', '') }}">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlUser">User Name</label></th>
                        <td>
                            <input name="mysqlUser" type="text" class="form-control" id="mysqlUser" value="{{ env('DB_USERNAME', '') }}">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlPass">Password</label></th>
                        <td>
                            <input name="mysqlPass" type="text" class="form-control" id="mysqlPass" value="{{ env('DB_PASSWORD', '') }}">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tablePrefix">Table 접두사</label></th>
                        <td>
                            <input name="tablePrefix" type="text" class="form-control" value="{{ env('DB_PREFIX', 'la_') }}" id="tablePrefix">
                            <span>가능한 변경하지 마십시오.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="ins_frm">
                <caption>최고관리자 정보입력</caption>
                <tbody>
                    <tr>
                        <th scope="row"><label for="adminEmail">Email</label></th>
                        <td>
                            <input name="adminEmail" type="text" class="form-control" value="admin@domain.com" id="adminEmail">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="adminPass">Password</label></th>
                        <td>
                            <input name="adminPass" type="text" class="form-control" id="adminPass">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="adminNick">Nickname</label></th>
                        <td>
                            <input name="adminNick" type="text" class="form-control" value="최고관리자" id="adminNick">
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>
                <strong class="st_strong">주의! 이미 {{ config('app.name') }}가 존재한다면 DB 자료가 망실되므로 주의하십시오.</strong><br>
                주의사항을 이해했으며, {{ config('app.name') }} 설치를 계속 진행하시려면 다음을 누르십시오.
            </p>
            <div class="inner_btn">
                <input type="submit" class="btn" value="다음">
            </div>
        </div>
    </form>
    @endif
    <script>
        function frm_install_submit(f)
        {
            if (f.appUrl.value == '')
            {
                alert('App URL 을 입력하십시오.'); f.appUrl.focus(); return false;
            }
            else if (f.mysqlPort.value == '')
            {
                alert('MySQL Port 를 입력하십시오.'); f.mysqlPort.focus(); return false;
            }
            else if (f.mysqlPort.value == '')
            {
                alert('MySQL Port 를 입력하십시오.'); f.mysqlPort.focus(); return false;
            }
            else if (f.mysqlUser.value == '')
            {
                alert('MySQL User Name 을 입력하십시오.'); f.mysqlUser.focus(); return false;
            }
            else if (f.mysqlDb.value == '')
            {
                alert('MySQL Database 를 입력하십시오.'); f.mysqlDb.focus(); return false;
            }
            else if (f.adminEmail.value == '')
            {
                alert('최고관리자 Email 을 입력하십시오.'); f.adminEmail.focus(); return false;
            }
            else if (f.adminPass.value == '')
            {
                alert('최고관리자 비밀번호를 입력하십시오.'); f.adminPass.focus(); return false;
            }
            else if (f.adminNick.value == '')
            {
                alert('최고관리자 닉네임을 입력하십시오.'); f.adminNick.focus(); return false;
            }

            return true;
        }
    </script>
</div>
@endsection
