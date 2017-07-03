<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;
use Cache;
use App\Point as AppPoint;

class Point extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'points';
    protected $dates = [ 'datetime', ];

    // 유저 모델과의 관계 설정
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // index 페이지에서 필요한 파라미터 가져오기
    public function getPointIndexParams($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $searchEmail = '';
        $query = '';
        $sum = Point::sum('point');

        if($kind) {
            if($kind == 'content') {
                $query = Point::where($kind, 'like', '%'.$keyword.'%');
            } else {
                $user = User::where( [$kind => $keyword] )->first();
                if($user) {
                    // 검색한 유저의 id로 포인트 테이블 조회
                    $query = Point::where(['user_id' => $user->id]);
                    $sum = $user->point;
                    $searchEmail = $user->email;
                } else {
                    // 유저가 없으므로 user_id를 0으로 해서 조회한다. (null error 방지용)
                    $query = Point::where(['user_id' => 0]);
                }
            }
        } else {
            $query = Point::select('*');
        }

        // 정렬
        if($order) {
            if($order == 'email') {
                $query = $query
                    ->selectRaw('points.*, users.email as email')
                    ->leftJoin('users', 'points.user_id', '=', 'users.id')
                    ->orderBy('email', $direction);
            } else {
                $query = $query->orderBy($order, $direction);
            }
        } else {
            $query = $query->orderBy('id', 'desc');
        }

        $points = $query->paginate(Cache::get("config.homepage")->pageRows);

        return [
            'points' => $points,
            'sum' => $sum,   // 모든 유저의 포인트합을 구한다.
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
            'searchEmail' => $searchEmail,
        ];
    }

    // (포인트 관리) 포인트 증감 설정
    public function givePoint($data)
    {
        $user = User::where('email', $data['email'])->first();

        if(!is_null($user)) {
            $relAction = 'admin-' . substr(bcrypt(Carbon::now()), 0, 15);

            // 포인트 부여
            if($user->point + $data['point'] < 0) {
                return ['message' => '포인트를 깎는 경우 현재 포인트보다 작으면 안됩니다.'];
            }
            $user->point += $data['point'];
            $user->save();

            // 포인트 내역에 기록
            $appPoint = new AppPoint();

            return $appPoint->insertPoint($user->id, $data['point'], $data['content'], '@passive', $user->email, $relAction);
        } else {
            return ['message' => '존재하는 회원 이메일이 아닙니다.'];
        }
    }

    // (포인트 관리) 선택 삭제
    public function deletePointOnAdmin($ids)
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

            foreach($laterPointList as $laterPoint) {
                $laterPoint->user_point -= $point->point;
                $laterPoint->save();
            }

            // 선택된 포인트 내역 삭제
            $point->delete($id);
        }
    }
}
