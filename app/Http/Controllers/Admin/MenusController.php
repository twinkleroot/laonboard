<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menu;

class MenusController extends Controller
{

    public $menuModel;

    public function __construct(Menu $menu)
    {
        $this->menuModel = $menu;
    }
    /**
     * 메뉴 설정 index view
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = $this->menuModel->getMenuIndexParams();

        return view('admin.menus.index', $params);
    }

    /**
     * 메뉴 추가 popup view
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $params = $this->menuModel->getMenuCreateParams($request);

        return view('admin.menus.create', $params);
    }

    /**
     * 메뉴 설정 저장
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Menu 테이블의 모든 데이터를 삭제하고 auto-incrementing ID를 0으로 초기화 한다.
        $this->menuModel->initMenu();

        // 입력된 폼을 분석해서 code를 생성하고 메뉴 정보를 저장
        $this->menuModel->saveMenu($request->all());

        return redirect(route('admin.menus.index'));
    }

    // 메뉴 추가 팝업창에 대상 선택에 따라서 view를 load하는 기능 (Ajax)
    public function result(Request $request)
    {
        $params = $this->menuModel->menuResult($request->get('type'));

        return view('admin.menus.result', $params);
    }

}
