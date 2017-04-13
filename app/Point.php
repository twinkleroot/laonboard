<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Config;
use App\User;
use DB;

class Point extends Model
{
    public $timestamps = false;

    protected $dates = [ 'datetime', ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 유저 모델과의 관계 설정
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // index 페이지에서 필요한 파라미터 가져오기
    public function getPointIndexParams()
    {
        $points = Point::orderBy('id', 'desc')->paginate(10);

        return [
            'points' => $points,
            'sum' => $this->sumPoint(),
            'kind' => '',
            'keyword' => '',
            'searchEmail' => '',
        ];
    }

    // 모든 유저들의 포인트 총합을 구한다.
    public function sumPoint()
    {
        // 각 유저의 현재 포인트를 가져온다.
        $users = Point::select(DB::raw('max(user_point) as max_point'))->groupBy('user_id')->get();

        // 유저들의 포인트 총합
        $sum = 0;
        foreach($users as $user) {
            $sum += $user->max_point;
        }

        return $sum;
    }

    public function givePoint($data)
    {
        $user = User::where('email', $data['email'])->first();

        if(!is_null($user)) {
            $rel_action = 'admin-' . substr(bcrypt(Carbon::now()), 0, 15);

            // 포인트 부여
            if($user->point + $data['point'] < 0) {
                return '포인트를 깎는 경우 현재 포인트보다 작으면 안됩니다.';
            }
            $user->point += $data['point'];
            $user->save();

            // 포인트 내역에 기록
            static::loggingPoint($user, $data['point'], '@passive', $rel_action, $data['content']);

            // 포인트 부여 성공
            return 'success';
        } else {
            return '존재하는 회원 이메일이 아닙니다.';
        }
    }

    public function deletePoint($ids)
    {
        $idArr = explode(',', $ids);

        foreach($idArr as $id) {
            // 유저 테이블의 point에 반영.
            $point = Point::find($id);
            $user = User::find($point->user_id);
            $user->point -= $point->point;
            $user->save();

            // 포인트 내역의 user_point에 반영
            $laterPointList = Point::where('id', '>', $point->id)
                                    ->where('user_id', $user->id)
                                    ->get();

            // dd($laterPointList);

            foreach($laterPointList as $laterPoint) {
                $laterPoint->user_point -= $point->point;
                $laterPoint->save();
            }

            // 선택된 포인트 내역 삭제
            $point->delete($id);
        }
    }

    // 포인트 테이블에 포인트 부여내역을 기록
    public static function loggingPoint($user, $pointToGive, $rel_table, $rel_action, $content)
    {
        Point::create([
            'user_id' => $user->id,
            'datetime' => Carbon::now(),
            'content' => $content,
            'point' => $pointToGive,
            'user_point' => $user->point,
            'expire_date' => date('9999-12-31'),
            'rel_table' => $rel_table,
            'rel_email' => $user->email,
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

    // 같은 건으로 포인트를 수령했는지 검사
    public static function checkPoint($rel_table, $rel_email, $rel_action)
    {
        return Point::where([
            'rel_table' => $rel_table,
            'rel_email' => $rel_email,
            'rel_action' => $rel_action,
        ])->first();
    }

    // 회원 가입 후 로그인 시키는 상태인지 검사
    public static function isUserJoin($user)
    {
        $point = Point::where('user_id', $user->id)
        ->orderBy('id', 'desc')
        ->first();

        if(str_contains($point->content, '회원가입')) {
            return true;
        }
        return false;
    }

}
