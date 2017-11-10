@extends('admin.layouts.basic')

@section('title')메뉴 설정 | {{ $config->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/jquery.tablednd.js') }}"></script>
@endsection

@section('content')
<form role="form" method="POST" action="{{ route('admin.menus.store') }}" onsubmit="menuFormSubmit()">
{{ csrf_field() }}
    <div class="body-head">
        <div class="pull-left">
            <h3>메뉴 설정</h3>
            <ul class="fl">
                <li class="admin">Admin</li>
                <li class="depth">환경 설정</li>
                <li class="depth">메뉴 설정</li>
            </ul>
        </div>
    </div>

    <div id="body_tab_type2">
        <span class="txt">웹사이트 메뉴를 설정합니다.</span>
        <div class="submit_btn">
            <button type="button" class="btn btn-sir" onclick="add_menu();">메뉴 추가</button>
            <input type="submit" class="btn btn-default" value="확인">
        </div>
    </div>

    <div class="body-contents">
        @if(Session::has('message'))
        <div id="adm_save">
            <span class="adm_save_txt">{{ Session::get('message') }}</span>
            <button onclick="alertclose()" class="adm_alert_close">
                <i class="fa fa-times"></i>
            </button>
        </div>
        @endif
        @if ($errors->any())
        <div id="adm_save">
            <span class="adm_save_txt">{{ $errors->first() }}</span>
            <button onclick="alertclose()" class="adm_alert_close">
                <i class="fa fa-times"></i>
            </button>
        </div>
        @endif

        <div id="adm_alert">
            <span class="adm_alert_txt">
                <strong>주의!</strong> 메뉴설정 작업 후 반드시 <strong>확인</strong>을 누르셔야 저장됩니다.
            </span>
            <div>
                <span class="adm_alert_txt">
                    앞의 핸들을 <strong>Drag & Drop</strong> 해서 메뉴 순서를 조정할 수 있습니다. 핸들을 더블클릭하면 서브 메뉴로 변경하거나, 주 메뉴로 변경할 수 있습니다.
                </span>
            </div>
        </div>

        <div id="menulist">
            <table class="table table-striped box" id="menuTable">
                <thead>
                    <tr>
                        <th>핸들</th>
                        <th>메뉴</th>
                        <th>링크</th>
                        <th>새창</th>
                        <th>사용</th>
                        {{-- <th>모바일사용</th> --}}
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($menus as $menu)
                    <tr class="menu_list menu_group_{{ substr($menu['code'], 0, 2) }}">
                        <td class="dragHandle" style=""></td>
                        <td class="text-center @if(strlen($menu['code']) == 4) sub_menu_class @endif @if($errors->get('name.'. $loop->index)) has-error @endif">
                            <input type="hidden" name="code[]" value="{{ substr($menu['code'], 0, 2) }}">
                            <div @if(strlen($menu['code']) == 4)class="depth2" @endif>
                                <input type="text" class="form-control required" name="name[]" value="{{ $menu['name']}}" />
                            </div>
                        </td>
                        <td class="text-center @if($errors->get('link.'. $loop->index)) has-error @endif">
                            <input type="text" class="form-control required" name="link[]" value="{{ $menu['link']}}" />
                        </td>
                        <td class="text-center">
                            <select name="target[]" class="form-control">
                                <option value='self' {{ $menu['target'] == 'self' ? 'selected' : '' }}>사용안함</option>
                                <option value='blank' {{ $menu['target'] == 'blank' ? 'selected' : '' }}>사용함</option>
                            </select>
                        </td>
                        <input type="hidden" class="form-control required" name="order[]" value="{{ $menu['order']}}"/>
                        <td class="text-center">
                            <select name="use[]" class="form-control">
                                <option value='1' {{ $menu['use'] == 1 ? 'selected' : '' }}>사용함</option>
                                <option value='0' {{ $menu['use'] == 0 ? 'selected' : '' }}>사용안함</option>
                            </select>
                        </td>
                        {{-- <td class="text-center">
                            <select name="mobile_use[]" class="form-control">
                                <option value='1' {{ $menu['mobile_use'] == 1 ? 'selected' : '' }}>사용함</option>
                                <option value='0' {{ $menu['mobile_use'] == 0 ? 'selected' : '' }}>사용안함</option>
                            </select>
                        </td> --}}
                        <td class="text-center">
                            <button type="button" class="btn btn-danger del_menu">삭제</button>
                        </td>
                    </tr>
                @empty
                    <tr id="empty_menu_list">
                        <td colspan="7">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>
<script>
var menuVal = 100400;
$(function(){
    // 관리의 삭제 버튼 클릭(한 건 삭제)
    $(document).on("click", ".del_menu", function() {
        if(!confirm("메뉴를 삭제하시겠습니까?")) {
            return false;
        }

        var $tr = $(this).closest("tr");

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

    initDragAndDropPlugin();

    $(document).on("dblclick", ".dragHandle", function(e) {
        e.preventDefault();
        $(this).siblings('td:first').toggleClass('sub_menu_class');
        $(this).siblings('td:first').find("input[name='name[]']").closest('div').toggleClass('depth2');

        var thisMenuCode = $(this).siblings('td:first').find("input[name='code[]']").val();
        var prevMenuCode = $(this).parents("tr").prev().find("input[name='code[]']").val();
        if(typeof(prevMenuCode) == 'undefined') {
            prevMenuCode = $(this).parents("tr").find("input[name='code[]']").val();
        }

        if(thisMenuCode == prevMenuCode) {	// 현재 2 depth 메뉴이고 1 depth 메뉴로 변경하려고 할 경우
            // 현재 코드에 10을 더하고 그 아래 있는 모든 메뉴들의 코드값도 10을 더한다.
            var code = $(this).parents("tr").find("input[name='code[]']").val();
            var codePlusTen = parseInt(code) + 10;
            $(this).parents("tr").find("input[name='code[]']").val(codePlusTen);
            $(this).parents("tr").nextAll().each(function(index,tr) {
                var nextCode = $(tr).find("input[name='code[]']").val();
                var nextCodePlusTen = parseInt(nextCode) + 10;
                $(tr).find("input[name='code[]']").val(nextCodePlusTen);
            });
        } else {	// 현재 1 depth 메뉴이고 2 depth 메뉴로 변경하려고 하는 경우
            var tmpCode = $(this).find("input[name='code[]']").val();
            $(this).siblings('td:first').find("input[name='code[]']").val(prevMenuCode);
            $(this).parents("tr").nextAll().each(function(index,tr) {
                var nextCode = $(tr).find("input[name='code[]']").val();
                if(tmpCode == nextCode) {
                    $(tr).find("input[name='code[]']").val(prevMenuCode);
                }
            });
        }
    });

});

function menuFormSubmit() {
    // 메인 메뉴와 서브메뉴의 code값을 넣는다.
    insertCode();
    // Drag & drop으로 메뉴 순서 정한거 DB의 order 값에 반영하기
    adjustOrder();
}

function insertCode() {
    var code = $("#menulist tr.menu_list").find("input[name='code[]']").first().val();
    $("#menulist tr.menu_list").each(function(index, tr) {
        if(!$(this).find("td.sub_menu_class").find("input[name='code[]']").val()) {
            code = parseInt(code) + 10;
        }
        $(this).find("input[name='code[]']").val(code);
    });
}

function adjustOrder() {
    var depthOneIndex = 0;
    var depthTwoIndex = 0;
    var code = 0;
    $("#menulist tr.menu_list").each(function() {
        if( code == $(this).find("input[name='code[]']").val() ) {
            $(this).find("input[name='order[]']").val(depthTwoIndex);
            depthTwoIndex++;
        } else {
            $(this).find("input[name='order[]']").val(depthOneIndex);
            depthOneIndex++;
            depthTwoIndex = 0;
            code = $(this).find("input[name='code[]']").val();
        }
    });
}

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
