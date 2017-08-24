<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Config;

class ConfigController extends Controller
{
    public $configModel;

    public function __construct(Config $configModel)
    {
        $this->middleware('super');

        $this->configModel = $configModel;
    }

    public function index()
    {
        $params = $this->configModel->getConfigIndexParams();

        return view('admin.configs.basic', $params);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $message = '';

        if($this->configModel->updateConfig($data)) {
            $message = '기본환경설정 변경이 완료되었습니다.';
        } else {
            $message = '기본환경설정 변경에 실패하였습니다.';
        }
        return redirect(route('admin.config'))->with('message', $message);
    }
}
