@extends('admin.admin')

@section('title')
    메뉴설정 | {{ $config->title }}
@endsection

@section('content')
<form role="form" method="POST" action="{{ route('admin.menus.store') }}">
{{ csrf_field() }}
    <div class="body-head">
        <div class="pull-left">
            <h3>메뉴설정</h3>
            <ul class="fl">
                <li class="admin">Admin</li>
                <li class="depth">환경설정</li>
                <li class="depth">메뉴설정</li>
            </ul>
        </div>
        <div class="pull-right">
            <ul class="mb_btn" style="margin-top:8px;">
                <li><button type="button" class="btn btn-sir" onclick="add_menu();">메뉴 추가</button></li>
                <li><input type="submit" class="btn btn-default" value="확인"></li>
            </ul>
        </div>
    </div>

    <div class="body-contents">
        @if(Session::has('message'))
          <div class="alert alert-info">
            {{ Session::get('message') }}
          </div>
        @endif

        <div class="alert alert-danger">
            주의! 메뉴설정 작업 후 반드시 확인을 누르셔야 저장됩니다.
        </div>

        <div id="menulist">
            <table class="table table-striped box">
                <thead>
                    <tr>
                        <th>메뉴</th>
                        <th>링크</th>
                        <th>새창</th>
                        <th>순서</th>
                        <th>PC사용</th>
                        <th>모바일사용</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                @if(count($menus) > 0)
                @foreach ($menus as $menu)
                    <tr class="menu_list menu_group_{{ substr($menu['code'], 0, 2) }}">
                        <td class="text-center @if(strlen($menu['code']) == 4) sub_menu_class @endif">
                            <input type="hidden" name="code[]" value="{{ substr($menu['code'], 0, 2) }}">
                            @if(strlen($menu['code']) == 4)
                                <div class="row">
                                    <div class="col-md-2">ㄴ</div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="name[]" value="{{ $menu['name']}}" />
                                    </div>
                                </div>
                            @else
                                <input type="text" class="form-control" name="name[]" value="{{ $menu['name']}}" />
                            @endif
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control" name="link[]" value="{{ $menu['link']}}" />
                        </td>
                        <td class="text-center">
                            <select name="target[]" class="form-control">
                                <option value='self' {{ $menu['target'] == 'self' ? 'selected' : '' }}>사용안함</option>
                                <option value='blank' {{ $menu['target'] == 'blank' ? 'selected' : '' }}>사용함</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control" name="order[]" value="{{ $menu['order']}}" size="5"/>
                        </td>
                        <td class="text-center">
                            <select name="use[]" class="form-control">
                                <option value='1' {{ $menu['use'] == 1 ? 'selected' : '' }}>사용함</option>
                                <option value='0' {{ $menu['use'] == 0 ? 'selected' : '' }}>사용안함</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="mobile_use[]" class="form-control">
                                <option value='1' {{ $menu['mobile_use'] == 1 ? 'selected' : '' }}>사용함</option>
                                <option value='0' {{ $menu['mobile_use'] == 0 ? 'selected' : '' }}>사용안함</option>
                            </select>
                        </td>
                        <td class="text-center">
                            @if(strlen($menu['code']) == 2)
                                <button type="button" class="btn btn-default add_sub_menu">추가</button>
                            @endif
                            <button type="button" class="btn btn-danger del_menu">삭제</button>
                        </td>
                    </tr>
                @endforeach
                @else
                    <tr id="empty_menu_list">
                        <td colspan="7">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</form>
<script>
var menuVal = 100400;
$(function(){
    // 관리의 추가 버튼 클릭(하위 메뉴 추가)
    $(document).on("click", ".add_sub_menu", function() {
        var code = $(this).closest("tr").find("input[name='code[]']").val().substr(0, 2);
        var url = "/admin/menus/create?code=" + code;
        window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");

        return false;
    });

    // 관리의 삭제 버튼 클릭(한 건 삭제)
    $(document).on("click", ".del_menu", function() {
        if(!confirm("메뉴를 삭제하시겠습니까?")) {
            return false;
        }

        var $tr = $(this).closest("tr");
        console.log($tr.find("td.sub_menu_class").length);
        if($tr.find("td.sub_menu_class").length > 0) {
            $tr.remove();
        } else {
            var code = $tr.find("input[name='code[]']").val().substr(0, 2);
            $("tr.menu_group_"+code).remove();
        }

        if($("#menulist tr.menu_list").length < 1) {
            var list = "<tr id=\"empty_menu_list\"><tdclass=\"text-center\" colspan=\"7\">자료가 없습니다.</td></tr>\n";
            $("#menulist table tbody").append(list);
        }
    });

});

// 메뉴 추가 버튼 클릭
function add_menu() {
    var max_code = base_convert(0, 10, 36);
    $("#menulist tr.menu_list").each(function() {
        var me_code = $(this).find("input[name='code[]']").val().substr(0, 2);
        if(max_code < me_code)
            max_code = me_code;
    });

    var url="/admin/menus/create?code=" + max_code + "&new=new"
    window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");
    return false;
}

// code 생성 함수
function base_convert(number, frombase, tobase) {
    //  discuss at: http://phpjs.org/functions/base_convert/
    // original by: Philippe Baumann
    // improved by: Rafał Kukawski (http://blog.kukawski.pl)
    //   example 1: base_convert('A37334', 16, 2);
    //   returns 1: '101000110111001100110100'

    return parseInt(number + '', frombase | 0).toString(tobase | 0);
}

</script>
@endsection
