<?php

namespace Modules\CustomMain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Gate;

class CustomMainController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $menuCode = ['custommain', 'r'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-custommain-index', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        $events = sortArray(config("event.mainContents"), 'priority');

        return view('modules.custommain.admin.index', ['events' => $events]);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $menuCode = ['custommain', 'w'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-custommain-update', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        $datas = array_except($request->all(), ['_method', '_token']);
        $events = config("event.mainContents");
        $config = new \ConfigWriter("event");

        foreach($datas as $key => $value) {
            $names = explode('-', $key);
            $first = reset($names);
            $last = last($names);
            $namespace = eventNamespace($first, $events[$first]);
            $path = explode("\\", $namespace);
            if(strtolower($path[0]) == 'app') {
                $location = app_path($path[1]). "/Config/event.php";
            } else {
                $location = module_path($path[1]). "/Config/event.php";
            }

            $config->set("mainContents.$first.$last", (int)$value);
            $config->save($location, $location, false);
        }

        $message = '메인 페이지 설정이 변경되었습니다.';

        return redirect()->back()->with('message', $message);
    }

}
