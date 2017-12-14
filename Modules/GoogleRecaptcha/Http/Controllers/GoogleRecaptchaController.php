<?php

namespace Modules\GoogleRecaptcha\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Ixudra\Curl\Facades\Curl;
use App\Models\Config;
use Cache;

class GoogleRecaptchaController extends Controller
{
    public $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    /**
     * 구글 리캡챠 서버쪽 검사
     * @return Response
     */
    public function googlerecaptcha(Request $request)
    {
        if(!cache('config.recaptcha')->googleInvisibleServer) {
            $message = "모듈 관리에서 자동등록방지(Google Invisible reCAPTCHA)키가 등록되지 않아서 진행할 수 없습니다. 관리자에게 문의하여 주십시오.";

            return [
                'message' => $message
            ];
        }
        $url = 'https://www.google.com/recaptcha/api/siteverify'.
                '?secret='. cache('config.recaptcha')->googleInvisibleServer.
                '&response='. $request['g-recaptcha-response'];
        $flag = json_decode(Curl::to($url)->get());

        $message = '';
        if(!$flag || !$flag->success) {
            $message = '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.';
        }

        return [
            'message' => $message
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $menuCode = ['googlerecaptcha', 'r'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-googlerecaptcha-index', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        return view('modules.googlerecaptcha.admin.index');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $menuCode = ['googlerecaptcha', 'w'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-googlerecaptcha-update', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        Cache::forget("config.recaptcha");

        $data = array_except($request->all(), ['_method', '_token']);
        $message = '';

        if($this->config->updateConfigByOne('recaptcha', $data)) {
            $message = '구글 리캡챠(Google Invisible reCAPTCHA) 설정을 변경하였습니다.';
        }

        return redirect()->back()->with('message', $message);
    }

}
