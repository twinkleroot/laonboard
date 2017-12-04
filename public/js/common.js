// .submitBtn 클래스가 적용된 버튼에 대한 이벤트
// (자동등록방지 등 모듈에서 submit 하기 전에 이 이벤트를 off하고 재정의 할 수 있다.)
$(function() {
    $(document).on('click', '.submitBtn', function(){
        var form = $(".submitBtn").closest('form');
        $(form).submit();
    });
});

// 전역 변수
var errmsg = "";
var errfld = null;

function htmlAutoBr(obj) {
    if (obj.checked) {
        var result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result) {
            obj.value = "html2";
        } else {
            obj.value = "html1";
        }
    } else {
        obj.value = "";
    }
}

function writeSubmit(useEditor, htmlUsable) {
    var subject = "";
    var content = "";
    var contentData = "";
    if(useEditor == 1 && htmlUsable == 1) {
        contentData = tinymce.get('content').getContent();
    } else {
        contentData = $('#content').val();
    }

    $.ajax({
        url: '/ajax/filter/board',
        type: 'post',
        data: {
            '_token' : window.Laravel.csrfToken,
            'subject' : $('#subject').val(),
            'content' : contentData
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            subject = data.subject;
            content = data.content;
        }
    });

    if(subject) {
        alert("제목에 금지단어 (" + subject + ") 가 포함되어 있습니다.");
        $('#subject').focus();
        return false;
    }

    if(content) {
        alert("내용에 금지단어 (" + content + ") 가 포함되어 있습니다.");
        tinymce.get('content').focus();
        return false;
    }

    return true;
}

// 닉네임 금지어 검사
function filterNickname()
{
    var nick = "";

    $.ajax({
        url: '/ajax/filter/user',
        type: 'post',
        data: {
            '_token' : window.Laravel.csrfToken,
            'nick' : $('#nick').val()
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            nick = data.nick;
        }
    });

    if(nick) {
        alert("닉네임에 금지단어 (" + nick + ") 가 포함되어 있습니다.");
        $('#nick').focus();
        return false;
    }

    return true;
}

function initDragAndDropPlugin() {
    $("#menuTable").tableDnD({
        dragHandle: ".dragHandle",
    });

    $('.dragHandle').mousedown(function(){
        $(this).css({
            'color' : '#ff6699',
            'background' : '#3e63d6'
        });
    }).mouseup(function(){
        $(this).css({
            'color' : '',
            'background' : ''
        });
    });
}

// 문자열에 특수문자가 들어가 있는지 검사
function checkStringFormat(string) {
    var stringRegx = /[~!@\#$%<>^&*\()\-=+_\’]/gi;
    var isValid = true;
    if(stringRegx.test(string)) {
        isValid = false;
    }

    return isValid;
}

// 체크박스로 업데이트할 값 배열에 담기
function toUpdateByCheckBox(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        var chkbox = $('input[id= ' + id + '_' + selected_id_array[i] + ']');
        if(chkbox.is(':checked')) {
            send_array[i] = chkbox.val();
        } else {
            send_array[i] = 0;
        }
    }

    return send_array;
}

// 셀렉트박스로 업데이트할 값 배열에 담기
function toUpdateBySelectOption(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('select[id=' + id + '_' + selected_id_array[i] + ']').val();
    }

    return send_array;
}

// 텍스트 입력으로 업데이트할 값 배열에 담기
function toUpdateByText(id, selected_id_array) {
    var send_array = Array();
    for(i=0; i<selected_id_array.length; i++) {
        send_array[i] = $('input[id=' + id + '_' + selected_id_array[i] + ']').val();
    }

    return send_array;
}

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

// 메일 보내기 팝업 띄우기
function winFormMail(href) {
    var newWin = window.open(href, 'winFormMail', 'left=100, top=100, width=600, height=600, scrollbars=1');
    newWin.focus();
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

// 플래시 메세지 창 가리기
function alertclose() {
    document.getElementById("adm_save").style.display = "none";
}

// 삭제 검사 확인
function del(href) {
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        document.location.href = href;
    }
}

// 삭제 검사 확인2
function del2(href, message) {
    if(confirm(message)) {
        document.location.href = href;
    } else {
        return false;
    }
}

// 삭제 POST 로 진행
function delPost(form) {
    event.preventDefault();

    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        document.getElementById(form).submit();
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
        if (ch.length > 4) {
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
