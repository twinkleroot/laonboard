// 전역 변수
var errmsg = "";
var errfld = null;

function winScrap(href) {
    var newWin = window.open(href, 'winScrap', 'left=100, top=100, width=600, height=600, scrollbars=1');
    newWin.focus();
}

// 삭제 검사 확인
function del(href)
{
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        document.location.href = href;
    }
}

function deleteConfirm()
{
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        return true;
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
