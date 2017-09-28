<!DOCTYPE html>
<html>
<head>
<title>전체 알림</title>
<link rel="stylesheet" type="text/css" href="http://jla.gnutest.com/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="http://jla.gnutest.com/font-awesome/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="http://jla.gnutest.com/themes/default/css/style.css">
<!-- blade에 넣어야 할 css -->
<link rel="stylesheet" type="text/css" href="http://jla.gnutest.com/themes/default/css/common.css">
<link rel="stylesheet" type="text/css" href="http://jla.gnutest.com/themes/default/css/pushmsg.css">
<!-- blade에 넣어야 할 css 끝 -->
<script src="http://jla.gnutest.com/js/jquery-3.1.1.min.js"></script>
<script src="http://jla.gnutest.com/js/common.js"></script>
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};

    $(function(){
        $('.gnb-li.dropdown').hover(function() {
            $(this).addClass('open');
        }, function() {
            $(this).removeClass('open');
        });
    });
</script>
</head>
<body>
<div id="contents">
    <!-- pushmsg 여기부터 -->
    <div id="pushmsg" class="container">
        <div class="bd_head">
            <span>전체 알림 총 18 건</span>
        </div>
        <div class="bd_btn">
            <button type="" class="btn btn-danger">모든알림삭제</button>
        </div>
        <div class="alert">
             알림 보관 기간은 60일 입니다.
        </div>
        <div class="pull-left bd_btn">
            <ul>
                <li><button class="btn btn-default">전체선택</button></li>
                <li><button class="btn btn-default">선택삭제</button></li>
                <li><button class="btn btn-default">읽음표시</button></li>
            </ul>
        </div>
        <div class="bd_btn">
            <ul>
                <li><button class="btn btn-sir">전체보기</button></li>
                <li><button class="btn btn-sir">읽은알림</button></li>
                <li><button class="btn btn-sir">안읽은알림</button></li>
            </ul>
        </div>
        <table class="table box">
            <tbody>
                <tr>
                    <td class="td_chk"><input type="checkbox" name=""></td>
                    <td class="td_mngsmall">16:58</td>
                    <td class="td_mngsmall"><span class="read">읽음</span></td>
                    <td>
                        <span class="bd_subject">공지글이당</span>
                    </td>
                    <td class="td_mngsmall td_del">
                        <a href="" class="list_del"><img src="//jla.gnutest.com/themes/default/images/ico_del.gif" alt="알림삭제"></a>
                    </td>
                </tr>
                <tr>
                    <td class="td_chk"><input type="checkbox" name=""></td>
                    <td class="td_mngsmall">09/28</td>
                    <td class="td_mngsmall"><span class="noread">안읽음</span></td>
                    <td>
                        <span class="bd_subject">공지글이당</span>
                    </td>
                    <td class="td_mngsmall td_del">
                        <a href="" class="list_del"><img src="//jla.gnutest.com/themes/default/images/ico_del.gif" alt="알림삭제"></a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="pull-left bd_btn">
            <ul>
                <li><button class="btn btn-default">전체선택</button></li>
                <li><button class="btn btn-default">선택삭제</button></li>
                <li><button class="btn btn-default">읽음표시</button></li>
            </ul>
        </div>
    </div>
    <!-- pushmsg 여기까지 -->
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="http://bootstrapk.com/dist/js/bootstrap.min.js"></script>
<script src="http://bootstrapk.com/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>