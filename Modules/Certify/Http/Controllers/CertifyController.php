<?php

namespace Modules\Certify\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Certify\Models\Certify;
use App\Models\Config;
use App\Models\User;
use Cache;
use Gate;

class CertifyController extends Controller
{
    public $certify;
    public $config;

    public function __construct(Certify $certify, Config $config)
    {
        $this->certify = $certify;
        $this->config = $config;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $menuCode = ['certify', 'r'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-certify-index', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        $params = [
            'configCert' => cache('config.cert') ? : $this->registerCertConfigCache(),
        ];

        return view('modules.certify.admin.index', $params);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $menuCode = ['certify', 'w'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-certify-update', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        $this->validate($request, $this->rules(), $this->messages());

        Cache::forget('config.cert');

        $data = $request->all();
        $configData = [
            'certUse' => $data['certUse'],
            // 'certIpin' => $data['certIpin'],
            'certHp' => $data['certHp'],
            'certKcbCd' => $data['certKcbCd'],
            'certLimit' => isset($data['certLimit']) ? $data['certLimit'] : 0,
            'certReq' => isset($data['certReq']) ? $data['certReq'] : 0,
        ];

        $this->config->updateConfigByOne('cert', $configData);
        $this->registerCertConfigCache();

        return redirect()->back()->with('message', '본인확인 설정이 변경되었습니다.');
    }

    private function registerCertConfigCache()
    {
        $cert = Config::where('name', 'config.cert')->first();
        if($cert) {
            $config = json_decode($cert->vars);
            Cache::forever("config.cert", $config);

            return $config;
        }

        return 0;
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'certUse' => 'bail|numeric|required',
            'certHp' => 'bail|alpha|nullable',
            'certKcbCd' => 'bail|alpha_num|nullable',
            'certLimit' => 'bail|numeric|required',
            'certReq' => 'bail|numeric|nullable',
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'certUse.required' => '본인확인을 선택해 주세요.',
            'certUse.numeric' => '본인확인에는 숫자만 들어갈 수 있습니다.',
            'certHp' => '휴대폰 본인확인은 영문자만 포함될 수 있습니다.',
            'certKcbCd' => '코리아크레딧뷰로 KCB 회원사 ID에는 영문자와 숫자만 포함될 수 있습니다.',
            'certLimit.required' => '본인확인 이용제한을 입력해 주세요.',
            'certLimit.numeric' => '본인확인 이용제한에는 숫자만 들어갈 수 있습니다.',
            'certReq.numeric' => '본인확인 필수에는 숫자만 들어갈 수 있습니다.',
        ];
    }

    // 휴대폰 본인 확인 서비스 진행 팝업 1
    public function kcbHpCert1(Request $request)
    {
        $hp = $this->certify->getHpConfig($request);
        $result = $this->certify->executeCmd($hp);

        $params = [
            'commonSvlUrl' => $hp['commonSvlUrl'],
            'targetId' => $hp['targetId'],
            'retcode' => $result['retcode'],
            'e_rqstData' => $result['e_rqstData'],
        ];

        return view('modules.certify.hp_cert1', $params);
    }

    // 휴대폰 본인 확인 서비스 진행 팝업 2
    public function kcbHpCert2(Request $request)
    {
        $params = $this->certify->callbackHpcert($request);

        return view('modules.certify.hp_cert2', $params);
    }

    // ajax - 회원 가입전 본인확인 여부 검사
    public function validateCertBeforeJoin(Request $request)
    {
        $message = '';
        if(cache('config.cert')->certUse && cache('config.cert')->certReq) {
            if( trim($request->certNo) != session()->get('ss_cert_no') || !session()->get('ss_cert_no') ) {
                $message = '회원가입을 위해서는 본인확인을 해주셔야 합니다.';
            }
        }

        return [
            'message' => $message,
        ];

    }

    // ajax - 같은 사람의 본인확인 데이터를 사용했는지 검사
    public function existCertData(Request $request)
    {
        $message = '';
        if(cache('config.cert')->certUse && session()->get('ss_cert_type') && session()->get('ss_cert_dupinfo')) {
            $checkUser = User::where('email', '<>', $request->email)->where('dupinfo', session()->get('ss_cert_dupinfo'))->first();
            if($checkUser) {
                $message = "입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원이메일 : ". $checkUser->email;
            }
        }

        return [
            'message' => $message,
        ];
    }

    // ajax - 사용자 데이터에 본인확인 데이터를 포함시킨다.
    public function mergeUserData(Request $request)
    {
        $certType = session()->get('ss_cert_type');
        $certNo = session()->get('ss_cert_no');
        $name = $request->filled('name') ? cleanXssTags(trim($request->name)) : null;
        $hp = $request->filled('hp') ? trim($request->hp) : null;
        $userInfo = [];
        if(cache('config.cert')->certUse && $certType && $certNo) {
            // 해시값이 같은 경우에만 본인확인 값을 저장한다.
            if( session()->get('ss_cert_hash') == md5($name.$certType.session()->get('ss_cert_birth').$certNo) ) {
                $userInfo = [
                    'hp' => $hp,
                    'certify' => $certType,
                    'adult' => session()->get('ss_cert_adult'),
                    'birth' => session()->get('ss_cert_birth'),
                    'sex' => session()->get('ss_cert_sex'),
                    'dupinfo' => session()->get('ss_cert_dupinfo'),
                    'name' => $name,
                ];
            }
        } else {
            $userInfo = [
                'hp' => $hp,
                'certify' => null,
                'adult' => 0,
                'birth' => null,
                'sex' => null,
                'name' => $name,
            ];
        }

        return [
            'userInfo' => $userInfo,
        ];
    }
}
