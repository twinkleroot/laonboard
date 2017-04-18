@extends('theme')

@section('title')
    메뉴설정 | {{ $config->title }}
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h2>메뉴설정</h2></div>
            <div class="panel-heading">
                주의! 메뉴설정 작업 후 반드시 확인을 누르셔야 저장됩니다.
            </div>
            <div class="panel-heading">
                <button type="button" class="btn btn-primary" onclick="add_menu();">메뉴 추가</button>
            </div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.menus.store') }}">
                {{ csrf_field() }}
                <div class="panel-body" id="menulist">
                    <table class="table table-hover">
                        <thead>
                            <th class="text-center">메뉴</th>
                            <th class="text-center">링크</th>
                            <th class="text-center">새창</th>
                            <th class="text-center">순서</th>
                            <th class="text-center">PC사용</th>
                            <th class="text-center">모바일사용</th>
                            <th class="text-center">관리</th>
                        </thead>
                        <tbody>
                        @if(count($menus) > 0)
                        @foreach ($menus as $menu)
                            <tr class="menu_list menu_group_{{ substr($menu['code'], 0, 2) }}">
                                <td class="text-center @if(strlen($menu['code']) == 4) sub_menu_class @endif">
                                    <input type="hidden" name="code[]" value="{{ substr($menu['code'], 0, 2) }}">
                                    @if(strlen($menu['code']) == 4) ㄴ @endif
                                    <input type="text" name="name[]" value="{{ $menu['name']}}" />
                                </td>
                                <td class="text-center">
                                    <input type="text" name="link[]" value="{{ $menu['link']}}" />
                                </td>
                                <td class="text-center">
                                    <select name="target[]">
                                        <option value='self' {{ $menu['target'] == 'self' ? 'selected' : '' }}>사용안함</option>
                                        <option value='blank' {{ $menu['target'] == 'blank' ? 'selected' : '' }}>사용함</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="text" name="order[]" value="{{ $menu['order']}}" size="5"/>
                                </td>
                                <td class="text-center">
                                    <select name="use[]">
                                        <option value='1' {{ $menu['use'] == 1 ? 'selected' : '' }}>사용함</option>
                                        <option value='0' {{ $menu['use'] == 0 ? 'selected' : '' }}>사용안함</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select name="mobile_use[]">
                                        <option value='1' {{ $menu['mobile_use'] == 1 ? 'selected' : '' }}>사용함</option>
                                        <option value='0' {{ $menu['mobile_use'] == 0 ? 'selected' : '' }}>사용안함</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    @if(strlen($menu['code']) == 2)
                                        <button type="button" class="add_sub_menu">추가</button>
                                    @endif
                                    <button type="button" class="del_menu">삭제</button>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr id="empty_menu_list">
                                <td class="text-center" colspan="7">
                                    자료가 없습니다.
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="submit" class="btn btn-primary" value="확인"/>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
$(function(){
    // 관리의 추가 버튼 클릭(하위 메뉴 추가)
    // $(document).on() : append 된 html의 클래스에도 event를 이어 준다.
    $(document).on("click", ".add_sub_menu", function() {
        var code = $(this).closest("tr").find("input[name='code[]']").val().substr(0, 2);
        var url = "/admin/menus/create?code=" + code;
        window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");

        return false;
    });

    // 관리의 삭제 버튼 클릭(한 건 삭제)
    $(document).on("click", ".del_menu", function() {
        if(!confirm("메뉴를 삭제하시겠습니까?"))
            return false;

        var $tr = $(this).closest("tr");
        if($tr.find("td.sub_menu_class").size() > 0) {
            $tr.remove();
        } else {
            var code = $tr.find("input[name='code[]']").val().substr(0, 2);
            $("tr.menu_group_"+code).remove();
        }

        if($("#menulist tr.menu_list").size() < 1) {
            var list = "<tr id=\"empty_menu_list\"><tdclass=\"text-center\" colspan=\"7\">자료가 없습니다.</td></tr>\n";
            $("#menulist table tbody").append(list);
        } else {
            // 리스트 줄 색깔 교차로 나오게 하기
            // $("#menulist tr.menu_list").each(function(index) {
            //     $(this).removeClass("bg0 bg1")
            //         .addClass("bg"+(index % 2));
            // });
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

  return parseInt(number + '', frombase | 0)
    .toString(tobase | 0);
}

</script>
@endsection
