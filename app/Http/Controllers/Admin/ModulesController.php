<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ModuleSource;
use App\Models\ManageAuth;

class ModulesController extends Controller
{
    public $module;

    public function __construct(ModuleSource $module)
    {
        $this->module = $module;
    }
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->module)) {
            abort(403, '설치된 모듈 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->module->getIndexParams($request);

        return view("admin.modules.index", $params);
    }

    /**
     * Display a listing of the module resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function module(Request $request)
    {
        $authModel = new ManageAuth();
        $params = $authModel->getIndexParams($request, 1);

        return view("admin.modules.manage", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        if (auth()->user()->cant('index', $this->module)) {
            abort(403, '설치된 모듈 상세보기에 대한 권한이 없습니다.');
        }

        $params = $this->module->getShowParams($name);

        return view("admin.modules.show", $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function edit($name)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $name)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (auth()->user()->cant('delete', $this->module)) {
            abort(403, '모듈 삭제에 대한 권한이 없습니다.');
        }

        $request = $this->mergeModuleName($request);

        $this->module->destroyModule($request);

        return redirect(route('admin.modules.index'));
    }

    /**
     * module active
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function active(Request $request)
    {
        if (auth()->user()->cant('update', $this->module)) {
            abort(403, '모듈 수정에 대한 권한이 없습니다.');
        }

        $request = $this->mergeModuleName($request);

        $this->module->activeModule($request);

        return redirect()->back();
    }

    /**
     * module inactive
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inactive(Request $request)
    {
        if (auth()->user()->cant('update', $this->module)) {
            abort(403, '모듈 수정에 대한 권한이 없습니다.');
        }

        $request = $this->mergeModuleName($request);

        $this->module->inactiveModule($request);

        return redirect()->back();
    }

    /**
     * merge module name in request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request
     */
    private function mergeModuleName($request)
    {
        $name = [];
        if($request->filled('chkId')) {
            $name = $request->chkId;
        } else if($request->filled('moduleName')) {
            $name = $request->moduleName;
        }

        $request->merge([
            'moduleName' => $name
        ]);

        return $request;
    }
}
