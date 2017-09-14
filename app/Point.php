<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Cache;
use DB;
use Exception;

class Point extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [ 'datetime' ];

    public function __construct()
    {
        $this->table = 'points';
    }

    // 유저 모델과의 관계 설정
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 커뮤니티 사용자별 포인트 내역
    public function getPointList($id)
    {
        $config = Cache::get("config.homepage");
        $points = Point::where('user_id', $id)->orderBy('id', 'desc')->paginate($config->pageRows);
        $sum = 0;
        foreach($points as $point) {
            $sum += $point->use_point;
        }
        return [
            'config' => $config,
            'points' => $points,
            'sum' => $sum,
        ];
    }

}
