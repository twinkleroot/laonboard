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

    public static function getRulePassword($name) {
        $config = Config::getConfig($name);
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
        // if($config->password_policy_sequence == 1) {
        //     $rulePieces[$index] = '^(012|123|234|345|456|567|678|789|890|901)';
        //     $index++;
        // }
        $ruleString = '/^(?=.*[a-z])' . implode($rulePieces) . '.{' . $config->password_policy_digits . ',}' .'/';

        array_push($ruleArr,  'regex:' . $ruleString);

        return $ruleArr;
    }
}
