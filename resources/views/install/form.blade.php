@extends('install.layout')

@section('title'){{ config('app.name')." 초기환경설정 2/3" }}@endsection

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
    <form action="{{ route('install.setup') }}" id="frm_install" method="post" autocomplete="off" onsubmit="return frm_install_submit(this);">
        <div class="ins_inner">
            <table class="ins_frm">
                <caption>App 정보입력</caption>
                <tbody>
                    <tr>
                        <th><label for="appUrl">App Url</label></th>
                        <td>
                            <input name="appUrl" type="text" class="form-control" value="{{ old('appUrl') ? : env('APP_URL', Request::root()) }}" id="appUrl">
                            @foreach ($errors->get('appUrl') as $message)
                            <strong>{{ $message }} (URL 형태)</strong>
                            @endforeach
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
                            <input name="mysqlHost" type="text" class="form-control" value="{{ old('mysqlHost') ? : env('DB_HOST', 'localhost') }}" id="mysqlHost">
                            @foreach ($errors->get('mysqlHost') as $message)
                            <strong>{{ $message }} (영문자, 숫자, 점(.))</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlPort">Port</label></th>
                        <td>
                            <input name="mysqlPort" type="text" class="form-control" value="{{ old('mysqlPort') ? : env('DB_PORT', '3306') }}" id="mysqlPort">
                            @foreach ($errors->get('mysqlPort') as $message)
                            <strong>{{ $message }}</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlDb">Database</label></th>
                        <td>
                            <input name="mysqlDb" type="text" class="form-control" id="mysqlDb" value="{{ old('mysqlDb') ? : env('DB_DATABASE', '') }}">
                            @foreach ($errors->get('mysqlDb') as $message)
                            <strong>{{ $message }} (영문자, 숫자, 언더스코어(_))</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlUser">User Name</label></th>
                        <td>
                            <input name="mysqlUser" type="text" class="form-control" id="mysqlUser" value="{{ old('mysqlUser') ? : env('DB_USERNAME', '') }}">
                            @foreach ($errors->get('mysqlUser') as $message)
                            <strong>{{ $message }} (영문자, 숫자, 언더스코어(_))</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="mysqlPass">Password</label></th>
                        <td>
                            <input name="mysqlPass" type="text" class="form-control" id="mysqlPass">
                            @foreach ($errors->get('mysqlPass') as $message)
                            <strong>{{ $message }}</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tablePrefix">Table 접두사</label></th>
                        <td>
                            <input name="tablePrefix" type="text" class="form-control" value="{{ old('tablePrefix') ? : env('DB_PREFIX', 'la_') }}" id="tablePrefix">
                            <span>가능한 변경하지 마십시오.</span>
                            @foreach ($errors->get('tablePrefix') as $message)
                            <p>
                                <strong>{{ $message }} (영문자로 시작하는 '영문자, 숫자, 언더스코어(_)'로 구성된 문자열)</strong>
                            </p>
                            @endforeach
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
                            <input name="adminEmail" type="text" class="form-control" value="{{ old('adminEmail') ? : 'admin@laonboard.com' }}" id="adminEmail">
                            @foreach ($errors->get('adminEmail') as $message)
                            <strong>{{ $message }}</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="adminPass">Password</label></th>
                        <td>
                            <input name="adminPass" type="text" class="form-control" id="adminPass">
                            @foreach ($errors->get('adminPass') as $message)
                            <strong>{{ $message }}</strong>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="adminNick">Nickname</label></th>
                        <td>
                            <input name="adminNick" type="text" class="form-control" value="{{ old('adminNick') ? : '최고관리자' }}" id="adminNick">
                            @foreach ($errors->get('adminNick') as $message)
                            <strong>{{ $message }}</strong>
                            @endforeach
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
