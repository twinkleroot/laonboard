// 전역 변수
var errmsg = "";
var errfld = null;

// 쿠키 입력
function set_cookie(name, value, expirehours, domain)
{
    var today = new Date();
    today.setTime(today.getTime() + (60*60*1000*expirehours));

    var cookie_value = escape(value) + "; path=/; expires=" + today.toGMTString() + ";";
    document.cookie = name + "=" + cookie_value;

    if (domain) {
        document.cookie += "domain=" + domain + ";";
    }
}

// 모두 선택
function checkAll(form) {
    var chk = document.getElementsByName("chkId[]");

    for (i=0; i<chk.length; i++) {
        chk[i].checked = form.chkAll.checked;
    }
}

// 선택한 항목들 id값 배열에 담기
function selectIdsByCheckBox(className) {
    var send_array = Array();
    var send_cnt = 0;
    var chkbox = $(className);

    for(i=0; i<chkbox.length; i++) {
        if(chkbox[i].checked == true) {
            send_array[send_cnt] = chkbox[i].value;
            send_cnt++;
        }
    }

    return send_array;
}

// 스크랩 팝업 띄우기
function winScrap(href) {
    var newWin = window.open(href, 'winScrap', 'left=100, top=100, width=600, height=600, scrollbars=1');
    newWin.focus();
}

// 쪽지 팝업 띄우기
function winMemo(href) {
    var newWin = window.open(href, 'winMemo', 'left=100, top=100, width=600, height=600, scrollbars=1');
    newWin.focus();
}

// 자기소개 팝업 띄우기
function winProfile(href) {
    var newWin = window.open(href, 'winProfile', 'left=100, top=100, width=600, height=600, scrollbars=1');
    newWin.focus();
}

// 삭제 검사 확인
function del(href)
{
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        document.location.href = href;
    }
}

// 삭제 검사 확인2
function del2(href, message)
{
    if(confirm(message)) {
        document.location.href = href;
    } else {
        return false;
    }
}

// 필드 검사
function check_field(fld, msg)
{
    if ((fld.value = trim(fld.value)) == "") {
        error_field(fld, msg);
    } else {
        clear_field(fld);
    }
    return;
}

// 필드 오류 표시
function error_field(fld, msg)
{
    if (msg != "") {
        errmsg += msg + "\n";
    }
    if (!errfld) {
        errfld = fld;
    }
    fld.style.background = "#BDDEF7";
}

// 필드를 깨끗하게
function clear_field(fld)
{
    fld.style.background = "#FFFFFF";
}

// 글숫자 검사
function check_byte(content, target)
{
    var i = 0;
    var cnt = 0;
    var ch = '';
    var cont = document.getElementById(content).value;

    for (i=0; i<cont.length; i++) {
        ch = cont.charAt(i);
        if (escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }
    // 숫자를 출력
    document.getElementById(target).innerHTML = cnt;

    return cnt;
}

// 자바스크립트로 PHP의 number_format 흉내를 냄
// 숫자에 , 를 출력
function number_format(data)
{

    var tmp = '';
    var number = '';
    var cutlen = 3;
    var comma = ',';
    var i;

    var sign = data.match(/^[\+\-]/);
    if(sign) {
        data = data.replace(/^[\+\-]/, "");
    }

    len = data.length;
    mod = (len % cutlen);
    k = cutlen - mod;
    for (i=0; i<data.length; i++)
    {
        number = number + data.charAt(i);

        if (i < data.length - 1)
        {
            k++;
            if ((k % cutlen) == 0)
            {
                number = number + comma;
                k = 0;
            }
        }
    }

    if(sign != null) {
        number = sign+number;
    }

    return number;
}
