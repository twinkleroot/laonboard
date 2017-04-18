<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>메뉴 추가 | {{ $config->title }}</title>

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>

</head>

<body>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">메뉴 추가</div>
            <div class="panel-body">
                {{ csrf_field() }}
                <table class="table table-hover">
                    <tr>
                        <th class="text-center">대상 선택</th>
                        <td>
                            <select name="type" id="type">
                                <option value="">직접입력</option>
                                <option value="group">게시판그룹</option>
                                <option value="board">게시판</option>
                                <option value="content">내용관리</option>
                            </select>
                        </td>
                    </tr>

                </table>

                <div id="menu_result">

                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $("#menu_result").load('/admin/menus/result', { type : '', _token : window.Laravel.csrfToken });

    $("#type").on("change", function() {
        var type = $(this).val();

        $("#menu_result").load('/admin/menus/result', { type : type, _token : window.Laravel.csrfToken } );
    });

    $(document).on("click", "#add_manual", function() {
        var name = $.trim($('#name').val());
        var link = $.trim($('#link').val());
        var code = {{ $code }};

        addMenuList(name, link, code);
    });

    $(document).on("click", ".add_select", function() {
        var name = $.trim($(this).siblings("input[name='subject[]']").val());
        var link = $.trim($(this).siblings("input[name='link[]']").val());
        var code = {{ $code }};

        addMenuList(name, link, code);
    });
});

function addMenuList(name, link, code)
{

    var menuList = $("#menulist", opener.document);
    var ms = new Date().getTime();
    var childIcon='';

    var sub_menu_class;
    @if($new == 'new')
        sub_menu_class = " class=\"text-center\"";
    @else
        sub_menu_class = " class=\"text-center sub_menu_class\"";
        // 임시로 ㄴ 으로 화면에 표시함.
        childIcon = "ㄴ";
    @endif

    var list = "<tr class=\"menu_list menu_group_"+ code + "\">";
    list += "<td" + sub_menu_class + ">";
    list += "<input type=\"hidden\" name=\"code[]\" value=\""+ code +"\">";
    list += childIcon + "<input type=\"text\" name=\"name[]\" value=\""+name+"\" id=\"name_"+ms+"\" required class=\"required frm_input full_input\">";
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<input type=\"text\" name=\"link[]\" value=\""+link+"\" id=\"link_"+ms+"\" required class=\"required frm_input full_input\">";
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<select name=\"target[]\" id=\"target_"+ms+"\">";
    list += "<option value=\"self\">사용안함</option>";
    list += "<option value=\"blank\">사용함</option>";
    list += "</select>";
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<input type=\"text\" name=\"order[]\" value=\"0\" id=\"order_"+ms+"\" required class=\"required frm_input\" size=\"5\">";
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<select name=\"use[]\" id=\"use_"+ms+"\">";
    list += "<option value=\"1\">사용함</option>";
    list += "<option value=\"0\">사용안함</option>";
    list += "</select>";
    list += "</td>";
    list += "<td class='text-center'>";
    list += "<select name=\"mobile_use[]\" id=\"mobile_use_"+ms+"\">";
    list += "<option value=\"1\">사용함</option>";
    list += "<option value=\"0\">사용안함</option>";
    list += "</select>";
    list += "</td>";
    list += "<td class='text-center'>";
    @if($new == 'new')
        list += "<button type=\"button\" class=\"add_sub_menu\">추가</button>";
    @endif
    list += "<button type=\"button\" class=\"del_menu\">삭제</button>";
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
        if(menuList.find("#empty_menu_list").length > 0)
            menuList.find("#empty_menu_list").remove();

        menuList.find("table tbody").append(list);
    }

    // 리스트 배경색 클래스 설정
    // $menulist.find("tr.menu_list").each(function(index) {
    //     $(this).removeClass("bg0 bg1")
    //         .addClass("bg"+(index % 2));
    // });

    window.close();
}

</script>
</body>
</html>
