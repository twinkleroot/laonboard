<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Cache;

class Config extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'configs';
    }

    // 비밀번호 정책 설정에 따라 비밀번호 정규식 조합
    public function getPasswordRuleByConfigPolicy() {
        $config = cache('config.join');
        $rulePieces = array();
        $ruleString = array();
        $ruleArr = [
            'required', 'confirmed',
        ];
        $index = 0;
        if($config->passwordPolicyUpper == 1) {     // 대문자를 1개 이상 포함할 때
            $rulePieces[$index] = '(?=.*[A-Z])';
            $index++;
        }
        if($config->passwordPolicyNumber == 1) {     // 숫자를 1개 이상 포함할 때
            $rulePieces[$index] = '(?=.*\d)';
            $index++;
        }
        if($config->passwordPolicySpecial == 1) {     // 특수문자를 1개 이상 포함할 때
            $rulePieces[$index] = '(?=.*[~!@#$%^&*()\-_=+])';
            $index++;
        }

        // 비밀번호 규칙 정규식 조합
        $ruleString = '/^(?=.*[a-z])' . implode($rulePieces) . '.{' . $config->passwordPolicyDigits . ',}/';

        array_push($ruleArr,  'regex:' . $ruleString);

        return $ruleArr;
    }

    // json형태로 저장된 설정을 배열형태로 변환하는 메소드
    public function pullConfig($config) {
        return json_decode($config->vars);
    }

    // 환경 설정 인덱스 페이지에 들어갈 데이터
    public function getConfigIndexParams()
    {
        $admins = User::where('level', 10)->get();

        return [
            'configHomepage' => cache("config.homepage"),
            'configBoard' => cache("config.board"),
            'configJoin' => cache("config.join"),
            'configCert' => cache("config.cert"),
            'configEmailDefault' => cache("config.email.default"),
            'configEmailBoard' => cache("config.email.board"),
            'configEmailJoin' => cache("config.email.join"),
            'configSns' => cache("config.sns"),
            'configExtra' => get_object_vars(cache("config.extra")),
            'admins' => $admins,
            'latestSkins' => getSkins('latest'),
            'searchSkins' => getSkins('search'),
            'userSkins' => getSkins('user'),
        ];
    }

    // 환경 설정 항목별 생성 함수 연결
    public function createConfigController($configName)
    {
        switch ($configName) {
            case 'homepage':
                return $this->createConfigHomepage();
            case 'board':
                return $this->createConfigBoard();
            case 'join':
                return $this->createConfigJoin();
            case 'cert':
                return $this->createConfigCert();
            case 'email.default':
                return $this->createConfigEmailDefault();
            case 'email.join':
                return $this->createConfigEmailJoin();
            case 'email.board':
                return $this->createConfigEmailBoard();
            case 'theme':
                return $this->createConfigTheme();
            case 'skin':
                return $this->createConfigSkin();
            case 'sns':
                return $this->createConfigSns();
            case 'extra':
                return $this->createConfigExtra();
            default:
                # code...
                break;
        }
    }

    // 회원 가입 설정을 config 테이블에 추가한다.
    public function createConfigHomepage()
    {
        $configArr = array (
            'title' => config('gnu.title'),
            'superAdmin' => config('gnu.superAdmin'),
            'usePoint' => config('gnu.usePoint'),
            'loginPoint' => config('gnu.loginPoint'),
            'memoSendPoint' => config('gnu.memoSendPoint'),
            'openDate' => config('gnu.openDate'),
            'newDel' => config('gnu.newDel'),
            'memoDel' => config('gnu.memoDel'),
            'popularDel' => config('gnu.popularDel'),
            'newRows' => config('gnu.newRows'),
            'pageRows' => config('gnu.pageRows'),
            // 'mobilePageRows' => config('gnu.mobilePageRows'),
            'writePages' => config('gnu.writePages'),
            // 'mobilePages' => config('gnu.mobilePages'),
            'newSkin' => config('gnu.newSkin'),
            'searchSkin' => config('gnu.searchSkin'),
            'useCopyLog' => config('gnu.useCopyLog'),
            'pointTerm' => config('gnu.pointTerm'),
        );

        return $this->createConfig('config.homepage', $configArr);
    }

    // 게시판 기본 설정을 config 테이블에 추가한다.
    public function createConfigBoard()
    {
        $configArr = array (
            'linkTarget' => config('gnu.linkTarget'),
            'readPoint' => config('gnu.readPoint'),
            'writePoint' => config('gnu.writePoint'),
            'commentPoint' => config('gnu.commentPoint'),
            'downloadPoint' => config('gnu.downloadPoint'),
            // 'searchPart' => config('gnu.searchPart'),
            'imageExtension' => config('gnu.imageExtension'),
            'flashExtension' => config('gnu.flashExtension'),
            'movieExtension' => config('gnu.movieExtension'),
            'filter' => config('gnu.filter'),
        );

        return $this->createConfig('config.board', $configArr);
    }

    // 회원 가입 설정을 config 테이블에 추가한다.
    public function createConfigJoin()
    {
        $configArr = array (
            'skin' => config('gnu.skin'),
            'nickDate' => config('gnu.nickDate'),
            'name' => config('gnu.name'),
            'homepage' => config('gnu.homepage'),
            'tel' => config('gnu.tel'),
            'hp' => config('gnu.hp'),
            'addr' => config('gnu.addr'),
            'signature' => config('gnu.signature'),
            'profile' => config('gnu.profile'),
            'joinLevel' => config('gnu.joinLevel'),
            'joinPoint' => config('gnu.joinPoint'),
            'leaveDay' => config('gnu.leaveDay'),
            'useMemberIcon' => config('gnu.useMemberIcon'),
            'iconLevel' => config('gnu.iconLevel'),
            'memberIconSize' => config('gnu.memberIconSize'),
            'memberIconWidth' => config('gnu.memberIconWidth'),
            'memberIconHeight' => config('gnu.memberIconHeight'),
            'recommend' => config('gnu.recommend'),
            'recommendPoint' => config('gnu.recommendPoint'),
            'banId' => config('gnu.banId'),
            'stipulation' => config('gnu.stipulation'),
            'privacy' => config('gnu.privacy'),
            'passwordPolicyUpper' => config('gnu.passwordPolicyUpper'),
            'passwordPolicyNumber' => config('gnu.passwordPolicyNumber'),
            'passwordPolicySpecial' => config('gnu.passwordPolicySpecial'),
            'passwordPolicyDigits' => config('gnu.passwordPolicyDigits'),
        );

        return $this->createConfig('config.join', $configArr);
    }

    // 본인 확인 설정 가져오기
    public function createConfigCert()
    {
        $configArr = array (
            'certUse' => config('gnu.certUse'),
            // 'certIpin' => config('gnu.certIpin'),
            'certHp' => config('gnu.certHp'),
            'certKcbCd' => config('gnu.certKcbCd'),
            'certLimit' => config('gnu.certLimit'),
            'certReq' => config('gnu.certReq'),
        );

        return $this->createConfig('config.cert', $configArr);
    }

    // 기본 메일 환경 설정을 config 테이블에 추가한다.
    public function createConfigEmailDefault()
    {
        $configArr = array (
            'emailUse' => config('gnu.emailUse'),
            'adminEmail' => config('gnu.adminEmail'),
            'adminEmailName' => config('gnu.adminEmailName'),
            'emailCertify' => config('gnu.emailCertify'),
            'formmailIsMember' => config('gnu.formmailIsMember'),
        );

        return $this->createConfig('config.email.default', $configArr);
    }
    // 게시판 글 작성 시 메일 설정을 config 테이블에 추가한다.
    public function createConfigEmailBoard()
    {
        $configArr = array (
            'emailWriteSuperAdmin' => config('gnu.emailWriteSuperAdmin'),
            'emailWriteGroupAdmin' => config('gnu.emailWriteGroupAdmin'),
            'emailWriteBoardAdmin' => config('gnu.emailWriteBoardAdmin'),
            'emailWriter' => config('gnu.emailWriter'),
            'emailAllCommenter' => config('gnu.emailAllCommenter'),
        );

        return $this->createConfig('config.email.board', $configArr);
    }
    // 회원가입 시 메일 설정을 config 테이블에 추가한다.
    public function createConfigEmailJoin()
    {
        $configArr = array (
            'emailJoinSuperAdmin' => config('gnu.emailJoinSuperAdmin'),
            'emailJoinUser' => config('gnu.emailJoinUser'),
        );

        return $this->createConfig('config.email.join', $configArr);
    }

    // 테마 설정 가져오기
    public function createConfigTheme()
    {
        $configArr = array (
            'name' => config('gnu.theme'),
        );

        return $this->createConfig('config.theme', $configArr);
    }

    // 개별 스킨 설정 가져오기
    public function createConfigSkin()
    {
        $configArr = array (
            'layout' => config('gnu.layoutSkin'),
            'board' => config('gnu.boardSkin'),
            'content' => config('gnu.contentSkin'),
            'mail' => config('gnu.mailSkin'),
            'memo' => config('gnu.memoSkin'),
            'latest' => config('gnu.latestSkin'),
        );

        return $this->createConfig('config.skin', $configArr);
    }

    // SNS 설정 가져오기
    public function createConfigSns()
    {
        $configArr = array (
            'kakaoKey' => null,
            'kakaoSecret' => null,
            'kakaoRedirect' => null,
            'naverKey' => null,
            'naverSecret' => null,
            'naverRedirect' => null,
            'facebookKey' => null,
            'facebookSecret' => null,
            'facebookRedirect' => null,
            'googleKey' => null,
            'googleSecret' => null,
            'googleRedirect' => null,
            'googleRecaptchaClient' => null,
            'googleRecaptchaServer' => null,
        );

        return $this->createConfig('config.sns', $configArr);
    }

    // 여분필드 설정 가져오기
    public function createConfigExtra()
    {
        $configArr = [];
        for($i=1; $i<=10; $i++) {
            $configArr = array_add($configArr, "subj_$i", null);
            $configArr = array_add($configArr, "value_$i", null);
        }

        return $this->createConfig('config.extra', $configArr);
    }

    // configs 테이블에 해당 row를 추가한다.
    public function createConfig($name, $configArr)
    {
        $config = new Config();
        $config->name = $name;
        $config->vars = json_encode($configArr);
        $config->save();
        return $config;
    }

    // 설정을 변경한다.
    public function updateConfig($data, $name='', $theme=0)
    {
        // DB엔 안들어가는 값은 데이터 배열에서 제외한다.
        $data = array_except($data, ['_token', '_method']);

        if($name) {
            Cache::forget("config.$name");
            return $this->updateConfigByOne($name, $data);
        }

           // 설정이 변경될 때 캐시를 지운다.
        Cache::forget("config.homepage");
        Cache::forget("config.board");
        Cache::forget("config.join");
        Cache::forget("config.cert");
        Cache::forget("config.email.default");
        Cache::forget("config.email.board");
        Cache::forget("config.email.join");
        Cache::forget("config.sns");
        Cache::forget("config.extra");

        $configData = [
            'title' => $data['title'],
            'superAdmin' => $data['superAdmin'],
            'usePoint' => isset($data['usePoint']) ? $data['usePoint'] : 0,
            'loginPoint' => $data['loginPoint'],
            'memoSendPoint' => $data['memoSendPoint'],
            'openDate' => $data['openDate'],
            'newDel' => $data['newDel'],
            'memoDel' => $data['memoDel'],
            'popularDel' => $data['popularDel'],
            'newRows' => $data['newRows'],
            'pageRows' => $data['pageRows'],
            // 'mobilePageRows' => $data['title'],
            'writePages' => $data['writePages'],
            // 'mobilePages' => $data['title'],
            'newSkin' => $data['newSkin'],
            'searchSkin' => $data['searchSkin'],
            'useCopyLog' => isset($data['useCopyLog']) ? $data['useCopyLog'] : 0,
            'pointTerm' => $data['pointTerm'],
        ];

        $this->updateConfigByOne('homepage', $configData);

        $configData = [
            'linkTarget' => $data['linkTarget'],
            'readPoint' => $data['readPoint'],
            'writePoint' => $data['writePoint'],
            'commentPoint' => $data['commentPoint'],
            'downloadPoint' => $data['downloadPoint'],
            'imageExtension' => $data['imageExtension'],
            'flashExtension' => $data['flashExtension'],
            'movieExtension' => $data['movieExtension'],
            'filter' => [ 0 => $data['filter'] ],
        ];

        $this->updateConfigByOne('board', $configData);

        $configData = [
            'skin' => $data['userSkin'],
            'nickDate' => $data['nickDate'],
            'name' => $data['name'],
            'homepage' => $data['homepage'],
            'tel' => $data['tel'],
            'hp' => $data['hp'],
            'addr' => $data['addr'],
            'signature' => $data['signature'],
            'profile' => $data['profile'],
            'joinLevel' => $data['joinLevel'],
            'joinPoint' => $data['joinPoint'],
            'leaveDay' => $data['leaveDay'],
            'useMemberIcon' => $data['useMemberIcon'],
            'iconLevel' => $data['iconLevel'],
            'memberIconSize' => $data['memberIconSize'],
            'memberIconWidth' => $data['memberIconWidth'],
            'memberIconHeight' => $data['memberIconHeight'],
            'recommend' => $data['recommend'],
            'recommendPoint' => $data['recommendPoint'],
            'loginPoint' => $data['loginPoint'],
            'banId' => [ 0 => $data['banId'] ],
            'stipulation' => $data['stipulation'],
            'privacy' => $data['privacy'],
            'passwordPolicyDigits' => $data['passwordPolicyDigits'],
            'passwordPolicySpecial' => isset($data['passwordPolicySpecial']) ? $data['passwordPolicySpecial'] : 0,
            'passwordPolicyUpper' => isset($data['passwordPolicyUpper']) ? $data['passwordPolicyUpper'] : 0,
            'passwordPolicyNumber' => isset($data['passwordPolicyNumber']) ? $data['passwordPolicyNumber'] : 0,
        ];

        $this->updateConfigByOne('join', $configData);

        $configData = [
            'certUse' => $data['certUse'],
            // 'certIpin' => $data['certIpin'],
            'certHp' => $data['certHp'],
            'certKcbCd' => $data['certKcbCd'],
            'certLimit' => isset($data['certLimit']) ? $data['certLimit'] : 0,
            'certReq' => isset($data['certReq']) ? $data['certReq'] : 0,
        ];

        $this->updateConfigByOne('cert', $configData);

        $configData = [
            'emailUse' => isset($data['emailUse']) ? $data['emailUse'] : 0,
            'adminEmail' => $data['adminEmail'],
            'adminEmailName' => $data['adminEmailName'],
            'emailCertify' => isset($data['emailCertify']) ? $data['emailCertify'] : 0,
            'formmailIsMember' => isset($data['formmailIsMember']) ? $data['formmailIsMember'] : 0,
        ];

        $this->updateConfigByOne('email.default', $configData);

        $configData = [
            'emailWriteSuperAdmin' => isset($data['emailWriteSuperAdmin']) ? $data['emailWriteSuperAdmin'] : 0,
            'emailWriteGroupAdmin' => isset($data['emailWriteGroupAdmin']) ? $data['emailWriteGroupAdmin'] : 0,
            'emailWriteBoardAdmin' => isset($data['emailWriteBoardAdmin']) ? $data['emailWriteBoardAdmin'] : 0,
            'emailWriter' => isset($data['emailWriter']) ? $data['emailWriter'] : 0,
            'emailAllCommenter' => isset($data['emailAllCommenter']) ? $data['emailAllCommenter'] : 0,
        ];

        $this->updateConfigByOne('email.board', $configData);

        $configData = [
            'emailJoinSuperAdmin' => isset($data['emailJoinSuperAdmin']) ? $data['emailJoinSuperAdmin'] : 0,
            'emailJoinUser' => isset($data['emailJoinUser']) ? $data['emailJoinUser'] : 0,
        ];

        $this->updateConfigByOne('email.join', $configData);

        $configData = [
            'naverKey' => isset($data['naverKey']) ? $data['naverKey'] : null,
            'naverSecret' => isset($data['naverSecret']) ? $data['naverSecret'] : null,
            'naverRedirect' => isset($data['naverRedirect']) ? $data['naverRedirect'] : null,
            'kakaoKey' => isset($data['kakaoKey']) ? $data['kakaoKey'] : null,
            'kakaoSecret' => isset($data['kakaoSecret']) ? $data['kakaoSecret'] : null,
            'kakaoRedirect' => isset($data['kakaoRedirect']) ? $data['kakaoRedirect'] : null,
            'facebookKey' => isset($data['facebookKey']) ? $data['facebookKey'] : null,
            'facebookSecret' => isset($data['facebookSecret']) ? $data['facebookSecret'] : null,
            'facebookRedirect' => isset($data['facebookRedirect']) ? $data['facebookRedirect'] : null,
            'googleKey' => isset($data['googleKey']) ? $data['googleKey'] : null,
            'googleSecret' => isset($data['googleSecret']) ? $data['googleSecret'] : null,
            'googleRedirect' => isset($data['googleRedirect']) ? $data['googleRedirect'] : null,
            'googleRecaptchaClient' => isset($data['googleRecaptchaClient']) ? $data['googleRecaptchaClient'] : null,
            'googleRecaptchaServer' => isset($data['googleRecaptchaServer']) ? $data['googleRecaptchaServer'] : null,
        ];

        $this->updateConfigByOne('sns', $configData);

        $configData = [];
        for($i=1; $i<=10; $i++) {
            $configData = array_add($configData, "subj_$i", isset($data["subj_$i"]) ? $data["subj_$i"] : null);
            $configData = array_add($configData, "value_$i", isset($data["value_$i"]) ? $data["value_$i"] : null);
        }

        return $this->updateConfigByOne('extra', $configData);

    }

    private function updateConfigByOne($name, $data)
    {
        $config = Config::where('name', 'config.'. $name)->first();

        // json 형식으로 되어 있는 설정값을 배열로 바꾼다.
        $originalData = json_decode($config->vars, true);

        // 업데이트할 설정값을 원래 설정 값에 덮어 씌운다.
        foreach($data as $key => $value) {
            $originalData[$key] = $data[$key];
        }

        // 다시 json 형식으로 바꿔서 config 테이블에 저장한다.
        $config->vars = json_encode($originalData);

        return $config->save();
    }

}
