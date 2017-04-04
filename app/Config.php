<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{

    protected $fillable = [
        'name', 'vars',
    ];

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
        if($config->password_policy_upper == 1) {
            $rulePieces[$index] = '(?=.*[A-Z])';
            $index++;
        }
        if($config->password_policy_number == 1) {
            $rulePieces[$index] = '(?=.*\d)';
            $index++;
        }
        if($config->password_policy_special == 1) {
            $rulePieces[$index] = '(?=.*[~!@#$%^&*()\-_=+])';
            $index++;
        }
        $ruleString = '/^(?=.*[a-z])' . implode($rulePieces) . '.{' . $config->password_policy_digits . ',}/';

        array_push($ruleArr,  'regex:' . $ruleString);

        return $ruleArr;
    }
}
