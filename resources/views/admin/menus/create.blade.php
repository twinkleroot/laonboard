<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>메뉴 추가 | {{ $config->title }}</title>
<!-- css -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/admin.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/jquery.tablednd.js') }}"></script>
</head>

<body class="popup">
<form class="form-horizontal">
{{ csrf_field() }}
<div id="header" class="popup">
<div class="container">
    <div class="title">
        <span>메뉴 추가</span>
    </div>

    <div class="cbtn">
        <button type="submit" id="add_manual" class="btn btn-sir">추가</button>
        <button type="button" class="btn btn-default" onclick="window.close();">창닫기</button>
    </div>
</div>
</div>

<div id="bd_menu_form" class="container">
    <div class="form-group">
        <label for="" class="col-sm-2 col-xs-3 control-label" style="text-align: left;">대상선택</label>
        <div class="col-sm-3 col-xs-4">
            <select class="form-control" name="type" id="type">
                <option value="">직접입력</option>
                <option value="group">게시판그룹</option>
                <option value="board">게시판</option>

                {{ fireEvent('menuSelectOption') }}

            </select>
        </div>
    </div>

    <div id="menu_result">

    </div>
</div>
</form>

<script>
$(function() {
    $("#menu_result").load('/admin/menus/result', { type : '', _token : window.Laravel.csrfToken });

    $("#type").on("change", function() {
        var type = $(this).val();

        // 옵션 선택 때 마다 list 불러오기
        $("#menu_result").load('/admin/menus/result', { type : type, _token : window.Laravel.csrfToken } );

        // 직접 입력일 때는 상단에 추가 버튼 숨기기
        if(type != '') {
            $("#add_manual").hide();
        } else {
            $("#add_manual").show();
        }
    });

    $(document).on("click", "#add_manual", function() {
        var name = $.trim($('#name').val());
        var link = $.trim($('#link').val());
        var code = "{{ $code }}";

        addMenuList(name, link, code);
    });

    $(document).on("click", ".add_select", function() {
        var name = $.trim($(this).siblings("input[name='subject[]']").val());
        var link = $.trim($(this).siblings("input[name='link[]']").val());
        var code = "{{ $code }}";

        addMenuList(name, link, code);
    });
});

function addMenuList(name, link, code)
{

    var menuList = $(opener.document).find("#menulist");
    var ms = new Date().getTime();
    var addedMenuName = "<input type=\"text\" class=\"form-control required\" name=\"name[]\" value=\""+name+"\" id=\"name_"+ms+"\" required class=\"required frm_input full_input\">";

    var sub_menu_class;
    @if($new == 'new')
        sub_menu_class = " class=\"text-center\"";
        addedMenuName = "<div>" + addedMenuName + "</div>";
    @else
        sub_menu_class = " class=\"text-center sub_menu_class\"";
        addedMenuName = "<div class=\"depth2\">" + addedMenuName + "</div>";
    @endif

    var list = "<tr class=\"menu_list menu_group_"+ code + "\">";
    list += "<td class=\"dragHandle\" style=\"cursor:move; background-color:#587ef6;\"></td>";
    list += "<td" + sub_menu_class + ">";
    list += "<input type=\"hidden\" name=\"code[]\" value=\""+ code +"\">";
    list += addedMenuName;
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<input type=\"text\" class=\"form-control required\" name=\"link[]\" value=\""+link+"\" id=\"link_"+ms+"\" required class=\"required frm_input full_input\">";
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<select class=\"form-control\" name=\"target[]\" id=\"target_"+ms+"\">";
    list += "<option value=\"self\">사용안함</option>";
    list += "<option value=\"blank\">사용함</option>";
    list += "</select>";
    list += "</td>";
    list += "<input type=\"hidden\" class=\"form-control required\" name=\"order[]\" value=\"0\" id=\"order_"+ms+"\" required class=\"required frm_input\">";
    list += "<td class='text-center'>";
    list += "<select class=\"form-control\" name=\"use[]\" id=\"use_"+ms+"\">";
    list += "<option value=\"1\">사용함</option>";
    list += "<option value=\"0\">사용안함</option>";
    list += "</select>";
    list += "</td>";
    // list += "<td class='text-center'>";
    // list += "<select class=\"form-control\" name=\"mobile_use[]\" id=\"mobile_use_"+ms+"\">";
    // list += "<option value=\"1\">사용함</option>";
    // list += "<option value=\"0\">사용안함</option>";
    // list += "</select>";
    // list += "</td>";
    list += "<td class='text-center'>";
    list += "<button type=\"button\" class=\"btn btn-danger del_menu\">삭제</button>";
    list += "</td>";
    list += "</tr>";


    // 메뉴의 마지막 위치 찾기 ( code 값 이용)
    var menuLast = null;

    if(code) {
        menuLast = menuList.find("tr.menu_group_"+code+":last");
    } else {
        menuLast = menuList.find("tr.menu_list:last");
    }

    if(menuLast.length > 0) {
        menuLast.after(list);
    } else {
        if(menuList.find("#empty_menu_list").length > 0) {
            menuList.find("#empty_menu_list").remove();
        }

        menuList.find("table tbody").append(list);
    }

    // 부모 창의 DragAndDrop Plugin 초기화
    opener.initDragAndDropPlugin();

    window.close();
}

</script>
</body>
</html>
