<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>이미지 업로드 | {{ Cache::get("config.homepage")->title }}</title>
<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/jquery.form.min.js') }}"></script>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<style type="text/css">
.file_input_textbox {
    float: left;
    width: 148px;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 400;
    border: 1px solid #ccc;
    color: #333;
    background: #fff;
    margin-right: 5px;
    border-radius: 4px;
}
.file_input_div {
    position: relative;
    width: 96px;
    height: 34px;
    overflow: hidden;
    margin: 0 5px 5px 0;
}
.file_input_button {
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    height: 34px;
    position: absolute;
    top: 0;

    color: #333;
    background: #fff;
}
.file_input_hidden {
    font-size: 45px;
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;

    filter: alpha(opacity=0);
    -ms-filter: "alpha(opacity=0)";
    -khtml-opacity: 0;
    -moz-opacity: 0;
}
.file_upload div {
    float: left;
}
.file_upload:after {
    content: "";
    display: block;
    clear: both;
}
.file_submit {
    margin-top: 10px;
}
.file_upload_btn button {
    width: 35px;
}
</style>
</head>
<body class="popup">

<form class="form-horizontal" role="form" id="imageForm" method="POST" action="{{ route('image.upload')}}" onsubmit="return false;" enctype="multipart/form-data">
    {{ csrf_field() }}
<div id="header" class="popup">
<div class="container">
    <div class="title">
        <span>이미지 업로드</span>
    </div>

    <div class="cbtn">
        <button class="btn btn-sir" id="ajaxSubmitBtn">업로드</button>
        <button class="btn btn-default" onclick="window.close();" >창닫기</button>
    </div>
</div>
</div>

<div id="photoUploader" class="container">
    <div class="file_upload" id="fileUploaderNo0">
        <div>
            <input type="text" id="fileNameNo0" class="file_input_textbox" readonly>
            <div class="file_input_div">
                <input type="button" value="이미지 선택" class="file_input_button">
                <input type="file" class="file_input_hidden" name="imageFile[]" id="imageFileNo0" onchange="javascript:document.getElementById('fileNameNo0').value = this.value.replace(/c:\\fakepath\\/i,'')">
            </div>
        </div>

        <div class="file_upload_btn">
            <button class="btn btn-sir addImageField" id="btnNo0">+</button>
        </div>
    </div>
</div>
</form>

</body>
</html>

<script>
var saveHtml = document.getElementById('photoUploader').innerHTML;
var count = 1;

$(function(){

    // 이미지 업로드 클릭
    $("#ajaxSubmitBtn").click(function(e){
        e.preventDefault();
        apply();
    });

    // + 버튼
    $(document).on('click', '.addImageField', function(){
        saveHtml = saveHtml.replace(/No+[0-9]/g, 'No'+count);
        document.getElementById('btnNo'+ (count-1)).innerHTML = '-';
        document.getElementById('btnNo'+ (count-1)).classList.remove('addImageField', 'btn-sir');
        document.getElementById('btnNo'+ (count-1)).classList.add('delImageField', 'btn-default');

        $("#photoUploader").append(saveHtml);
        count++;
    });

    // - 버튼
    $(document).on('click', '.delImageField', function(){
        document.getElementById(this.id).parentElement.parentElement.remove();
    });

});

function apply()
{
    // ajaxSubmit Option
    options = {
        success      : applyAfter,  // ajaxSubmit 후처리 함수
        dataType     : 'json'       // 데이터 타입 json
    };

    $("#imageForm").ajaxSubmit(options);
}

// ajaxSubmit 후처리 함수
function applyAfter(data, statusText, xhr, $form)
{
    if (statusText == "success") {
        // ajax 통신 성공 후 처리영역
        insertImagePathToEditor(data)
    } else {
        // ajax 통신 실패 처리영역
        alert(statusText);
    }
}

// 에디터 안으로 이미지 포함시키기
function insertImagePathToEditor(data) {
    // html 태그 구성
    var html = '';
    for(var i=0; i<data.length; i++) {
        html += "<img src='"+ data[i]+"' ><br style='clear:both;'>";
    }

    opener.tinymce.activeEditor.execCommand("mceInsertContent",'false', html);
    window.close();
}

</script>
