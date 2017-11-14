// 본인확인 인증창 호출
function certify_win_open(type, url)
{
    if(type == 'kcb-ipin')
    {
        var popupWindow = window.open( url, "kcbPop", "left=200, top=100, status=0, width=450, height=550" );
        popupWindow.focus();
    }
    else if(type == 'kcb-hp')
    {
        var popupWindow = window.open( url, "auth_popup", "left=200, top=100, width=430, height=590, scrollbar=yes" );
        popupWindow.focus();
    }
    else if(type == 'kcp-hp')
    {
        var return_gubun;
        var width  = 410;
        var height = 500;

        var leftpos = screen.width  / 2 - ( width  / 2 );
        var toppos  = screen.height / 2 - ( height / 2 );

        var winopts  = "width=" + width   + ", height=" + height + ", toolbar=no,status=no,statusbar=no,menubar=no,scrollbars=no,resizable=no";
        var position = ",left=" + leftpos + ", top="    + toppos;
        var AUTH_POP = window.open(url,'auth_popup', winopts + position);
    }
    else if(type == 'lg-hp')
    {
        var popupWindow = window.open( url, "auth_popup", "left=200, top=100, width=400, height=400, scrollbar=yes" );
        popupWindow.focus();
    }
}

// 인증체크
function cert_confirm()
{
    var type;
    var val = document.userForm.certType.value

    switch(val) {
        case "ipin":
            type = "아이핀";
            break;
        case "hp":
            type = "휴대폰";
            break;
        default:
            return true;
    }

    if(confirm("이미 "+type+"으로 본인확인을 완료하셨습니다.\n\n이전 인증을 취소하고 다시 인증하시겠습니까?"))
        return true;
    else
        return false;
}

// 회원 가입전 본인확인 여부 검사
function validateCertBeforeJoin()
{
    var message = "";

    $.ajax({
        url: '/certify/validate',
        type: 'post',
        data: {
            '_token' : window.Laravel.csrfToken,
            'certNo' : $('#certNo').val(),
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            message = data.message;
        }
    });

    if(message) {
        alert(message);

        return false;
    }

    return true;
}

// 같은 사람의 본인확인 데이터를 사용했는지 검사
function existCertData()
{
    var message = "";

    $.ajax({
        url: '/certify/exist',
        type: 'post',
        data: {
            '_token' : window.Laravel.csrfToken,
            'email' : $('#email').val()
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            message = data.message;
        }
    });

    if(message) {
        alert(message);

        return false;
    }

    return true;
}

// 사용자 데이터에 본인확인 데이터를 포함시킨다.
function mergeUserData()
{
    var userInfo = [];
    $.ajax({
        url: '/certify/merge',
        type: 'post',
        data: {
            '_token' : window.Laravel.csrfToken,
            'name' : $("#name").val(),
            'hp' : $("#hp").val()
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            userInfo = data.userInfo;
        }
    });

    if(userInfo) {
        $("#certify").val(userInfo['certify']);
        $("#adult").val(userInfo['adult']);
        $("#birth").val(userInfo['birth']);
        $("#sex").val(userInfo['sex']);
        $("#dupinfo").val(userInfo['dupinfo']);
    }

}
