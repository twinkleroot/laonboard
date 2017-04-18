<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Config;

class ConfigController extends Controller
{
    public $configModel;

    public function __construct(Config $configModel)
    {
        $this->middleware('level:10');

        $this->configModel = $configModel;
    }

    public function index()
    {
        $params = $this->configModel->getConfigIndexParams();

        return view('admin.configs.index', $params);
    }

    public function update($name, Request $request)
    {
        $data = $request->all();
        $message;

        switch ($name) {
            case 'homepage':
                if($this->configModel->updateConfig($data, $name)) {
                    $message = '홈페이지 기본 환경설정 변경이 완료되었습니다.';
                } else {
                    $message = '홈페이지 기본 환경설정 변경에 실패하였습니다.';
                }
                break;
            case 'join':
                if($this->configModel->updateConfig($data, $name)) {
                    $message = '회원가입 설정 변경이 완료되었습니다.';
                } else {
                    $message = '회원가입 설정 변경에 실패하였습니다.';
                }
                break;
            case 'board':
                if($this->configModel->updateConfig($data, $name)) {
                    $message = '게시판 기본 설정 변경이 완료되었습니다.';
                } else {
                    $message = '게시판 기본 설정 변경에 실패하였습니다.';
                }
                break;
            default:
                # code...
                break;
        }

        return redirect(route('admin.config'))->with('message', $message);
    }
}
