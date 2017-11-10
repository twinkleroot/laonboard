<?php

namespace Modules\Certify\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use File;

class Certify extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public $okPath;

    public function __construct()
    {
        $this->table = 'cert_history';
        $this->okPath = storage_path('okname');
    }

    public function getHpConfig($request)
    {
        $memId = cache('config.cert')->certKcbCd;
        $inTpBit = '0';
        $name = 'x';
        $birthday = 'x';
        $gender = 'x';
        $ntvFrnrTpCd = 'x';
        $mblTelCmmCd = 'x';
        $mbphnNo = 'x';
        $svcTxSeqno = date('ymdHis', time()) . str_pad((int)(microtime()*100), 2, "0", STR_PAD_LEFT);;
        $clientIp = $request->server('SERVER_ADDR');
        $rsv1 = '0';
        $rsv2 = '0';
        $rsv3 = '0';
        $hsCertMsrCd = '10';
        $hsCertRqstCausCd = '00';
        $returnMsg = 'x';
        $returnUrl = route('certify.kcb.hp2');

        $p = @parse_url($request->server('HTTP_HOST'));
        if(isset($p['host']) && $p['host']) {
            $clientDomain = $p['host'];
        } else {
            $clientDomain = $request->server('SERVER_NAME');
        }
        $clientDomain = escapeshellarg($clientDomain);

        if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            if(PHP_INT_MAX == 2147483647) { // 32-bit
                $exe = $this->okPath.'/bin/okname';
            } else {
                $exe = $this->okPath.'/bin/okname_x64';
            }
        } else {
            if(PHP_INT_MAX == 2147483647) {// 32-bit
                $exe = $this->okPath.'/bin/okname.exe';
            } else {
                $exe = $this->okPath.'/bin/oknamex64.exe';
            }
        }

        $logPath = $this->okPath.'/log';
        $targetId = '';

        if(cache('config.cert')->certUse == 2) {
            // 실서비스일 경우
            $endPointURL = 'http://safe.ok-name.co.kr/KcbWebService/OkNameService';
            $commonSvlUrl = 'https://safe.ok-name.co.kr/CommonSvl';
            $endPointUrl = 'http://safe.ok-name.co.kr/KcbWebService/OkNameService';
        } else {
            // 테스트일 경우
            $endPointURL = 'http://tsafe.ok-name.co.kr:29080/KcbWebService/OkNameService';
            $commonSvlUrl = 'https://tsafe.ok-name.co.kr:2443/CommonSvl';
            $endPointUrl = 'http://tsafe.ok-name.co.kr:29080/KcbWebService/OkNameService';
        }

        $option = "Q";

        $cmd = "$exe $svcTxSeqno \"$name\" $birthday $gender $ntvFrnrTpCd $mblTelCmmCd $mbphnNo $rsv1 $rsv2 $rsv3 \"$returnMsg\" $returnUrl $inTpBit $hsCertMsrCd $hsCertRqstCausCd $memId $clientIp $clientDomain $endPointURL $logPath $option";

        $ip = $request->ip();
        session()->put("ss_{$ip}_exe", $exe);
        session()->put("ss_{$ip}_endPointUrl", $endPointUrl);
        session()->put("ss_{$ip}_logPath", $logPath);

        return [
            'exe' => $exe,
            'cmd' => $cmd,
            'targetId' => $targetId,
            'commonSvlUrl' => $commonSvlUrl,
        ];
    }

    public function executeCmd($hp)
    {
        /**************************************************************************
        okname 실행
        **************************************************************************/
        $exe = $hp['exe'];
        $cmd = $hp['cmd'];

        exec($cmd, $out, $ret);		//cmd 실행

        if($ret == 127) {
            abort(405, '모듈실행 파일이 존재하지 않습니다.\\n\\n'.basename($exe).' 파일이 '.$this->okPath.'/bin 안에 있어야 합니다.');
        }
        if($ret == 126) {
            File::chmod($exe, 755);
            exec($cmd, $out, $ret);
        }
        if($ret == -1) {
            abort(405, '모듈실행 파일의 실행권한이 없습니다.\\n\\ncmd.exe의 IUSER 실행권한이 있는지 확인하여 주십시오.');
        }

        /**************************************************************************
        okname 응답 정보
        **************************************************************************/
        if($ret != 0) {
            if($ret <=200) {
                abort(405, sprintf("B%03d", $ret));
            } else {
                abort(405, sprintf("S%03d", $ret));
            }
        }

        return [
            'retcode' => $out[0],		// 결과코드
            'retmsg' => $out[1],		// 결과메시지
            'e_rqstData' => $out[2],	// 암호화된요청데이터
        ];
    }

    // 생년월일 본인 확인서비스 결과 콜백 함수
    public function callbackHpcert($request)
    {
        /* 공통 리턴 항목 */
        // $idcfMbrComCd           =   $memId;
        $idcfMbrComCd           =   $request->idcf_mbr_com_cd;			// 고객사코드
        $hsCertSvcTxSeqno       =   $request->hs_cert_svc_tx_seqno;		// 거래번호
        $rqstSiteNm             =   $request->rqst_site_nm;	        	// 접속도메인
        $hsCertRqstCausCd       =   $request->hs_cert_rqst_caus_cd; 	// 인증요청사유코드 2byte  (00:회원가입, 01:성인인증, 02:회원정보수정, 03:비밀번호찾기, 04:상품구매, 99:기타)

        $resultCd               =   $request->result_cd;            	// 결과코드
        $resultMsg              =   $request->result_msg;           	// 결과메세지
        $certDtTm               =   $request->cert_dt_tm;           	// 인증일시

        if($resultCd != 'B000') {
            abort(405, '휴대폰 본인확인 중 오류가 발생했습니다. 오류코드 : '.$resultCd.'\\n\\n문의는 코리아크레딧뷰로 고객센터 02-708-1000 로 해주십시오.');
        }

        /**************************************************************************
         * 모듈 호출    ; 생년월일 본인 확인서비스 결과 데이터를 복호화한다.
         **************************************************************************/
        $encInfo = $request->encInfo;
        if(preg_match('~[^0-9a-zA-Z+/=]~', $encInfo, $match)) {
            abort(405, "입력 값 확인이 필요합니다");
        }

        //KCB서버 공개키
        $WEBPUBKEY = trim($request->WEBPUBKEY);
        if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBPUBKEY, $match)) {
            abort(405, "입력 값 확인이 필요합니다");
        }

        //KCB서버 서명값
        $WEBSIGNATURE = trim($request->WEBSIGNATURE);
        if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBSIGNATURE, $match)) {
            abort(405, "입력 값 확인이 필요합니다");
        }

        // ########################################################################
        // # 암호화키 파일 설정 (절대경로) - 파일은 주어진 파일명으로 자동 생성 됨
        // ########################################################################
        $keypath = $this->okPath.'/key/safecert_'.$idcfMbrComCd.'.key';

        $cpubkey = $WEBPUBKEY;    //server publickey
        $csig = $WEBSIGNATURE;    //server signature

        // ########################################################################
        // # 로그 경로 지정 및 권한 부여 (절대경로)
        // # 옵션값에 'L'을 추가하는 경우에만 로그가 생성됨.
        // ########################################################################
        $option = 'SU';

        $ip = $request->ip();
        $exe = session()->get("ss_{$ip}_exe");
        $endPointUrl = session()->get("ss_{$ip}_endPointUrl");
        $logPath = session()->get("ss_{$ip}_logPath");
        // 명령어
        $cmd = "$exe $keypath $idcfMbrComCd $endPointUrl $WEBPUBKEY $WEBSIGNATURE $encInfo $logPath $option";

        // 실행
        exec($cmd, $out, $ret);

        $userEmail = auth()->user() ? auth()->user()->email : '';

        $this->createCertHistory($userEmail, 'kcb', 'hp', $request);

        if($ret == 0) {
            // 결과라인에서 값을 추출
            foreach($out as $a => $b) {
                if($a < 17) {
                    $field[$a] = $b;
                }
            }
        } else {
            if($ret <=200) {
                abort(405, sprintf("B%03d", $ret));
            } else {
                abort(405, sprintf("S%03d", $ret));
            }
        }

        // 인증결과처리
        $name = $field[7];
        $reqNum = $field[12];
        $birth = $field[8];
        $dupinfo = $field[4];
        $phoneNo = hyphenHpNumber($reqNum);

        $result= $this->checkExistDupInfo($userEmail, $dupinfo);
        if($result) {
            abort(405, "입력하신 본인확인 정보로 가입된 내역이 존재합니다.\\n회원이메일 : ". $result->email);
        }

        // hash 데이터
        $certType = 'hp';
        $md5CertNo = md5($reqNum);
        $hashData = md5($name.$certType.$birth.$md5CertNo);

        // 성인인증결과
        $adultDay = Carbon::now()->subYears(19)->format("Ymd");
        $adult = ((int)$birth <= (int)$adultDay) ? 1 : 0;

        session()->put('ss_cert_type', $certType);
        session()->put('ss_cert_no', $md5CertNo);
        session()->put('ss_cert_hash', $hashData);
        session()->put('ss_cert_adult', $adult);
        session()->put('ss_cert_birth', $birth);
        session()->put('ss_cert_sex', ($field[9] == 1 ? 'M' : 'F'));
        session()->put('ss_cert_dupinfo', $dupinfo);

        return [
            'certType' => $certType,
            'name' => $name,
            'hp' => $phoneNo,
            'certNo' => $md5CertNo,
        ];
    }

    private function createCertHistory($email, $company, $method, $request)
    {
        $values = [
            'user_email' => $email,
            'company' => $company,
            'method' => $method,
            'ip' => $request->server('REMOTE_ADDR'),
            'date' => Carbon::now()->toDateString(),
            'time' => Carbon::now()->toTimeString(),
        ];

        $cert = Certify::insert($values);
    }

    public function checkExistDupInfo($email='', $dupinfo)
    {
        return \App\Models\User::where('email', '<>', $email)->where('dupinfo', $dupinfo)->first();
    }

}
