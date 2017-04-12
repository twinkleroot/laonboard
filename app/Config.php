<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    // name 컬럼값으로 배열로 된 설정 가져오는 메서드
    public static function getConfig($name) {
        $configJson = Config::where([
            'name' => $name
        ])->first();

        return json_decode($configJson->vars);
    }

    // 비밀번호 정책 설정에 따라 비밀번호 정규식 조합
    public static function getRulePassword($name, $config) {
        $rulePieces = array();
        $ruleString = array();
        $ruleArr = [
            'required', 'confirmed',
        ];
        $index = 0;
        if($config->passwordPolicyUpper == 1) {
            $rulePieces[$index] = '(?=.*[A-Z])';
            $index++;
        }
        if($config->passwordPolicyNumber == 1) {
            $rulePieces[$index] = '(?=.*\d)';
            $index++;
        }
        if($config->passwordPolicySpecial == 1) {
            $rulePieces[$index] = '(?=.*[~!@#$%^&*()\-_=+])';
            $index++;
        }
        $ruleString = '/^(?=.*[a-z])' . implode($rulePieces) . '.{' . $config->passwordPolicyDigits . ',}/';

        array_push($ruleArr,  'regex:' . $ruleString);

        return $ruleArr;
    }

    // 설정 이름으로 설정 값을 가져온다.
    public function getConfigByName($name)
    {
        return Config::where('name', '=', $name)->first();
    }

    // 회원 가입 설정을 config 테이블에 추가한다.
    public function createConfigJoin()
    {
        $configArr = array (
          'emailCertify' => config('gnu.emailCertify'),
          'nickDate' => config('gnu.nickDate'),
          'openDate' => config('gnu.openDate'),
          'name' => config('gnu.name'),
          'homepage' => config('gnu.homepage'),
          'tel' => config('gnu.tel'),
          'hp' => config('gnu.hp'),
          'addr' => config('gnu.addr'),
          'signature' => config('gnu.signature'),
          'profile' => config('gnu.profile'),
          'recommend' => config('gnu.recommend'),
          'joinLevel' => config('gnu.joinLevel'),
          'joinPoint' => config('gnu.joinPoint'),
          'recommendPoint' => config('gnu.recommendPoint'),
          'loginPoint' => config('gnu.loginPoint'),
          'banId' => config('gnu.banId'),
          'stipulation' => config('gnu.stipulation'),
          'privacy' => config('gnu.privacy'),
          'passwordPolicyUpper' => config('gnu.passwordPolicyUpper'),
          'passwordPolicyNumber' => config('gnu.passwordPolicyNumber'),
          'passwordPolicySpecial' => config('gnu.passwordPolicySpecial'),
          'passwordPolicyDigits' => config('gnu.passwordPolicyDigits'),
        );

        return Config::create([
            'name' => 'config.join',
            'vars' => json_encode($configArr)
        ]);
    }

    // 게시판 기본 설정을 config 테이블에 추가한다.
    public function createConfigBoard()
    {
        $configArr = array (
          'delaySecond' => config('gnu.delaySecond'),
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

        return Config::create([
            'name' => 'config.board',
            'vars' => json_encode($configArr)
        ]);
    }

    // 설정을 변경한다.
    public function updateConfig($data, $name)
    {
        $config;

        // DB엔 안들어가도 되는 값은 데이터 배열에서 제외한다.
        $data = array_except($data, ['_token']);
        $data = array_except($data, ['_method']);

        $config = $this->getConfigByName('config.' . $name);

        // 회원 가입 설정 일 때
        if($name == 'join') {
            $data['banId'] = [ 0 => $data['banId'] ];
        } else if($name == 'board') {
            $data['filter'] = [ 0 => $data['filter'] ];
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
