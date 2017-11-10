<?php

namespace Modules\CustomLayout\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Gate;

class CustomLayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $menuCode = ['customlayout', 'r'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-customlayout-index', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        $headerLefts = sortArray(config("event.headerLefts"), 'priority');
        $headerContents = sortArray(config("event.headerContents"), 'priority');
        $footerContents = sortArray(config("event.footerContents"), 'priority');

        $params = [
            'headerLefts' => $headerLefts,
            'headerContents' => $headerContents,
            'footerContents' => $footerContents,
        ];

        return view('modules.customlayout.admin.index', ['events' => $params]);
    }

    /**
     * Ajax
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $menuCode = ['customlayout', 'w'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-customlayout-update', getManageAuthModel($menuCode))) {
            return  [
                'message' => '최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.',
                'location' => '/admin/index'
            ];
        }

        $datas = array_except($request->all(), ['_method', '_token']);
        $config = new \ConfigWriter("event");

        return $this->updateConfigs($datas, $config);
    }

    private function updateConfigs($datas, $config)
    {
        $hookPoint = $datas['hookPoint'];
        $eventName = $datas['eventName'];
        $name = $datas['propertyName'];
        $value = $datas['propertyValue'];
        $events = config("event.$hookPoint");

        $namespace = eventNamespace($eventName, $events[$eventName]);
        $path = explode("\\", $namespace);
        if(strtolower($path[0]) == 'app') {
            $location = app_path($path[1]). "/Config/event.php";
        } else {
            $location = module_path($path[1]). "/Config/event.php";
        }

        $config->set("$hookPoint.$eventName.$name", (int)$value);
        try{
            $config->save($location, $location, false);
        } catch(Exception $e) {}

        // create message
        $description = $events[$eventName]['description'];
        $nameKorean = ($name == 'use') ? '사용여부' : '우선순위';
        if($name == 'use') {
            $value = $value ? '사용함' : '사용안함';
        }
        $success = $description. "의 ". $nameKorean. "설정이 ". $value. "으로 변경되었습니다.";

        return [ "success" => $success ];
    }

}
