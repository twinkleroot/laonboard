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
    public $table='configs';

    // 비밀번호 정책 설정에 따라 비밀번호 정규식 조합
    public function getPasswordRuleByConfigPolicy() {
        $config = Cache::get('config.join');
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
            'configHomepage' => Cache::get("config.homepage"),
            'configJoin' => Cache::get("config.join"),
            'configBoard' => Cache::get("config.board"),
            'configEmailDefault' => Cache::get("config.email.default"),
            'configEmailBoard' => Cache::get("config.email.board"),
            'configEmailJoin' => Cache::get("config.email.join"),
            'configCert' => Cache::get("config.cert"),
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
            case 'cert':
                return $this->createConfigCert();
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
            'newRows' => config('gnu.newRows'),
            'pageRows' => config('gnu.pageRows'),
            'mobilePageRows' => config('gnu.mobilePageRows'),
            'writePages' => config('gnu.writePages'),
            'mobilePages' => config('gnu.mobilePages'),
            'newSkin' => config('gnu.newSkin'),
            'searchSkin' => config('gnu.searchSkin'),
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
            'searchPart' => config('gnu.searchPart'),
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

	// 개별 스킨 설정 가져오기
    public function createConfigCert()
    {
        $configArr = array (
            'certUse' => config('gnu.certUse'),
            'certIpin' => config('gnu.certIpin'),
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

    // configs 테이블에 해당 row를 추가한다.
    public function createConfig($name, $configArr)
    {
        return Config::create([
            'name' => $name,
            'vars' => json_encode($configArr)
        ]);
    }

    // 설정을 변경한다.
    public function updateConfig($data, $name, $theme = 0)
    {
        // DB엔 안들어가는 값은 데이터 배열에서 제외한다.
        $data = array_except($data, ['_token', '_method']);

        $config = Config::where('name', 'config.'. $name)->first();

        if($name == 'homepage') {       // 홈페이지 기본 환경 설정 일때
            Cache::forget("config.homepage");   // 설정이 변경될 때 캐시를 지운다.
            if( !$theme ) {
                $data = array_add($data, 'usePoint', isset($data['usePoint']) ? $data['usePoint'] : 0);
            }
        } else if($name == 'join') {    // 회원 가입 설정 일 때
            Cache::forget("config.join");   // 설정이 변경될 때 캐시를 지운다.
            if( !$theme ) {
                $data['banId'] = [ 0 => $data['banId'] ];
                $data = array_add($data, 'passwordPolicySpecial', isset($data['passwordPolicySpecial']) ? $data['passwordPolicySpecial'] : 0);
                $data = array_add($data, 'passwordPolicyUpper', isset($data['passwordPolicyUpper']) ? $data['passwordPolicyUpper'] : 0);
                $data = array_add($data, 'passwordPolicyNumber', isset($data['passwordPolicyNumber']) ? $data['passwordPolicyNumber'] : 0);
            }
        } else if($name == 'board') {   // 게시판 기본 설정일 때
            Cache::forget("config.board");  // 설정이 변경될 때 캐시를 지운다.
            $data['filter'] = [ 0 => $data['filter'] ];
        } else if($name == 'email.default') {   // 기본 메일 환경 설정 일때
            Cache::forget("config.email.default");  // 설정이 변경될 때 캐시를 지운다.
            $data = array_add($data, 'emailUse', isset($data['emailUse']) ? $data['emailUse'] : 0);
            $data = array_add($data, 'emailCertify', isset($data['emailCertify']) ? $data['emailCertify'] : 0);
            $data = array_add($data, 'formmailIsMember', isset($data['formmailIsMember']) ? $data['formmailIsMember'] : 0);
        } else if($name == 'email.board') { // 게시판 글 작성 시 메일 설정일 때
            Cache::forget("config.email.board");  // 설정이 변경될 때 캐시를 지운다.
            $data = array_add($data, 'emailWriteSuperAdmin', isset($data['emailWriteSuperAdmin']) ? $data['emailWriteSuperAdmin'] : 0);
            $data = array_add($data, 'emailWriteGroupAdmin', isset($data['emailWriteGroupAdmin']) ? $data['emailWriteGroupAdmin'] : 0);
            $data = array_add($data, 'emailWriteBoardAdmin', isset($data['emailWriteBoardAdmin']) ? $data['emailWriteBoardAdmin'] : 0);
            $data = array_add($data, 'emailWriter', isset($data['emailWriter']) ? $data['emailWriter'] : 0);
            $data = array_add($data, 'emailAllCommenter', isset($data['emailAllCommenter']) ? $data['emailAllCommenter'] : 0);
        } else if($name == 'email.join') {  // 회원가입 시 메일 설정일 때
            Cache::forget("config.email.join");  // 설정이 변경될 때 캐시를 지운다.
            $data = array_add($data, 'emailJoinSuperAdmin', isset($data['emailJoinSuperAdmin']) ? $data['emailJoinSuperAdmin'] : 0);
            $data = array_add($data, 'emailJoinUser', isset($data['emailJoinUser']) ? $data['emailJoinUser'] : 0);
        } else if($name == 'theme') {
            Cache::forget("config.theme");
        } else if($name == 'skin') {
            Cache::forget("config.skin");
        } else if($name == 'cert') {
            Cache::forget("config.cert");
			$data = array_add($data, 'certLimit', isset($data['certLimit']) ? $data['certLimit'] : 0);
			$data = array_add($data, 'certReq', isset($data['certReq']) ? $data['certReq'] : 0);
        }

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
