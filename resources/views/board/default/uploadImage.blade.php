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

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.form.min.js') }}"></script>

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">

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

    <!-- js -->
</head>
<body>

<div id="header">
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

<div id="photo_uploader" class="container">
    <form class="form-horizontal" role="form" id="imageForm" method="POST" action="{{ route('image.upload')}}" onsubmit="return false;" enctype="multipart/form-data">
    {{ csrf_field() }}
	<div class="file_upload" id="file_uploader0">
		<div>
			<input type="text" id="fileName" class="file_input_textbox" readonly>
			<div class="file_input_div">
				<input type="button" value="이미지 선택" class="file_input_button">
				<input type="file" class="file_input_hidden" name="imageFile[]" id="imageFile0" onchange="javascript:document.getElementById('fileName').value = this.value">
			</div>
		</div>

        <div class="file_upload_btn">
			<button class="btn btn-sir" onclick="addImageField()">+</button>
		</div>
		{{-- <div class="file_upload_btn">
			<button class="btn btn-default">-</button>
		</div> --}}
	</div>
    </form>
</div>

</body>
</html>

<script>
$(function(){

    // 이미지 업로드 클릭
    $("#ajaxSubmitBtn").click(function(e){
        e.preventDefault();
        apply();
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

var count = 1;

function insertImagePathToEditor(data) {
    // html 태그 구성
    var html = '';
    for(var i=0; i<data.length; i++) {
        html += "<img src='"+ data[i]+"' ><br style='clear:both;'>";
    }

    opener.tinymce.activeEditor.execCommand("mceInsertContent",'false', html);
    window.close();
}

function addImageField() {
    var saveHtml = document.getElementById('file_uploader'+count).innerHTML;
    var idStr = "imageFile" + count;
    var html = "<tr><td><input type=\"file\" name=\"imageFile[]\" id='" + idStr + "' /></td>"
                + "<td><button type=\"button\" onclick=\"addImageField()\">+</button></td>"
                + "<td><button type=\"button\" onclick=\"delImageField('" + count + "')\">-</button></td>"
                + "</tr>";
    $("#photo_uploader").append(html);
    count++;
}

function delImageField(count) {
    $("#imageFile"+count.toString()).parents('tr').remove();
}

</script>
