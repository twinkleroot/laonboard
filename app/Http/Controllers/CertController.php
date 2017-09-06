<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cert;
use File;

class CertController extends Controller
{
    public $cert;

    public function __construct(Cert $cert)
    {
        $this->cert = $cert;
    }
    // 휴대폰 본인 확인 서비스 1
    public function kcbHpCert1(Request $request)
    {
        $hp = $this->cert->getHpConfig($request);
        $result = $this->cert->executeCmd($hp);

        $params = [
            'commonSvlUrl' => $hp['commonSvlUrl'],
            'targetId' => $hp['targetId'],
            'retcode' => $result['retcode'],
            'e_rqstData' => $result['e_rqstData'],
        ];

        return view('user.hp_cert1', $params);
    }

    // 휴대폰 본인 확인 서비스 2
    public function kcbHpCert2(Request $request)
    {
        $params = $this->cert->callbackHpcert($request);

        return view('user.hp_cert2', $params);
    }
}
