<?php

namespace App\Models;

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
    protected $table = 'points';
    protected $dates = [ 'datetime' ];

    public function __construct()
    {
    }

    // 유저 모델과의 관계 설정
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
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
                $query = Point::with('user')->where($kind, 'like', '%'.$keyword.'%');
            } else {
                $user = User::where($kind, $keyword)->first();
                if($user) {
                    // 검색한 유저의 id로 포인트 테이블 조회
                    $query = Point::with('user')->where(['user_id' => $user->id]);
                    $sum = $user->point;
                    $searchEmail = $user->email;
                } else {
                    // 유저가 없으므로 user_id를 0으로 해서 조회한다. (null error 방지용)
                    $query = Point::where(['user_id' => 0]);
                }
            }
        } else {
            $query = Point::with('user');
        }

        // 정렬
        if($order) {
            if($order == 'email') {
                $query = $query
                    ->select('points.*', 'users.email as email')
                    ->leftJoin('users', 'points.user_id', '=', 'users.id')
                    ->orderBy('email', $direction);
            } else {
                $query = $query->orderBy($order, $direction);
            }
        } else {
            $query = $query->orderBy('id', 'desc');
        }

        $points = $query->paginate(Cache::get("config.homepage")->pageRows);

        foreach($points as $point) {
            if(!str_contains($point->rel_table, '@')) {
                $board = Board::getBoard($point->rel_table, 'table_name');
                if($board) {
                    $point->rel_table = $board->id;
                }
            }

            if(isDemo() && $point->user->id != auth()->user()->id) {
                $point->user->nick = invisible($point->user->nick);
                $point->user->email = invisible($point->user->email);
            }
        }

        $queryString = "?kind=$kind&keyword=$keyword&page=". $points->currentPage();

        return [
            'points' => $points,
            'sum' => $sum,   // 모든 유저의 포인트합을 구한다.
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
            'searchEmail' => $searchEmail,
            'queryString' => $queryString,
        ];
    }

    // (포인트 관리) 포인트 증감 설정
    public function givePoint($request)
    {
        $user = User::where('email', $request->email)->first();

        if($user) {
            $relAction = 'admin-' . substr(bcrypt(Carbon::now()), 0, 15);

            // 포인트 부여
            if($user->point + $request->point < 0) {
                abort(500, '포인트를 깎는 경우 현재 포인트보다 작으면 안됩니다.');
            }
            $user->point += $request->point;
            $user->save();

            // 포인트 내역에 기록
            insertPoint($user->id, $request->point, $request->content, '@passive', $user->email, $relAction);
        } else {
            abort(500, '존재하는 회원 이메일이 아닙니다.');
        }
    }

    // (포인트 관리) 선택 삭제
    public function deletePointOnAdmin($ids)
    {
        $idArr = explode(',', $ids);

        foreach($idArr as $id) {
            // 포인트 내역 정보
            $point = Point::find($id);
            if($point->point < 0) {
                $userId = $point->user_id;
                $usePoint = abs($point->point);

                if($point->rel_table == '@expire') {
                    deleteExpirePoint($userId, $usePoint);
                } else {
                    deleteUsePoint($userId, $usePoint);
                }
            } else {
                if($point->use_point > 0) {
                    insertPoint($userId, $point->use_point, $point->id);
                }
            }

            // 포인트 내역 삭제
            Point::destroy($id);

            // 포인트 내역의 user_point에 반영
            Point::where('id', '>', $point->id)
            ->where('user_id', $point->user_id)
            ->decrement('user_point', $point->point);

            // User->point에 반영
            $user = $point->user;
            $user->point = getPointSum($user->id);
            $user->save();
        }
    }

}
