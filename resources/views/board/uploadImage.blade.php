<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
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

</head>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">이미지 업로드</div>
            <form class="form-horizontal" role="form" id="imageForm" method="POST" action="{{ route('image.upload')}}" onsubmit="return false;" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="panel-body">
                    <table class="table table-hover">
                        <tbody class="imageField">
                            <tr>
                                <td>
                					<input type="file" name="imageFile[]" id="imageFile0"/>
                				</td>
                                <td>
                                    <button type="button" onclick="addImageField()">+</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="button" id="ajaxSubmitBtn" class="btn btn-primary" value="이미지 업로드"/>
                    <input type="button" class="btn btn-primary" onclick="window.close();" value="창닫기"/>
                </div>
            </form>
        </div>
    </div>
</div>
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
    var idStr = "imageFile" + count;
    var html = "<tr><td><input type=\"file\" name=\"imageFile[]\" id='" + idStr + "' /></td>"
                + "<td><button type=\"button\" onclick=\"addImageField()\">+</button></td>"
                + "<td><button type=\"button\" onclick=\"delImageField('" + count + "')\">-</button></td>"
                + "</tr>";
    $(".imageField").append(html);
    count++;
}

function delImageField(count) {
    $("#imageFile"+count.toString()).parents('tr').remove();
}

</script>
