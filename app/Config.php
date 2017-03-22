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
}
