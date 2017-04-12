<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Config;

class Point extends Model
{
    public $timestamps = false;

    protected $dates = ['expire_date', 'email_certify', 'nick_date', 'open_date', ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 포인트 테이블에 포인트 부여내역을 기록하는 메서드
    public static function givePoint($pointToGive, $rel_table, $rel_email, $rel_action, $content)
    {
        $nowDate = Carbon::now()->toDateString();
        $config = Config::getConfig('config.join');

        Point::create([
            'user_email' => $rel_email,
            'datetime' => Carbon::now(),
            'content' => $content,
            'point' => $pointToGive,
            'expire_date' => date('9999-12-31'),
            'user_point' => $pointToGive,
            'rel_table' => $rel_table,
            'rel_email' => $rel_email,
            'rel_action' => $rel_action,
        ]);
    }

    // 부여할 포인트점수를 구하는 메서드
    public static function pointType($pointType)
    {
        $point = 0;
        $config = Config::getConfig('config.join');

        if($pointType == 'join') {
            $point = $config->joinPoint;
        } else if($pointType == 'recommend') {
            $point = $config->recommendPoint;
        } else if($pointType == 'login') {
            $point = $config->loginPoint;
        }

        return $point;
    }

    // 같은 건으로 포인트를 수령했는지 검사하는 메서드
    public static function checkPoint($rel_table, $email, $action)
    {
        return Point::where([
            'rel_table' => $rel_table,
            'rel_email' => $email,
            'rel_action' => $action,
        ])->first();
    }
}
