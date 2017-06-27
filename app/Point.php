<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;
use DB;
use Cache;

class Point extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

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
                return '포인트를 깎는 경우 현재 포인트보다 작으면 안됩니다.';
            }
            $user->point += $data['point'];
            $user->save();

            // 포인트 내역에 기록
            static::loggingPoint($user, $data['point'], '@passive', $user->email, $relAction, $data['content']);

            // 포인트 부여 성공
            return 'success';
        } else {
            return '존재하는 회원 이메일이 아닙니다.';
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

    // 부여할 포인트점수를 구하는 메서드
    public static function pointType($pointType)
    {
        $point = 0;
        $configJoin = Cache::get("config.join");
        $configHomepage = Cache::get("config.homepage");

        if($pointType == 'join') {
            $point = $configJoin->joinPoint;
        } else if($pointType == 'recommend') {
            $point = $configJoin->recommendPoint;
        } else if($pointType == 'login') {
            $point = $configHomepage->loginPoint;
        } else {
            $point = $pointType;
        }

        return $point;
    }

    // 같은 건으로 포인트를 수령했는지 검사
    public static function checkPoint($relTable, $relEmail, $relAction)
    {
        return Point::where([
            'rel_table' => $relTable,
            'rel_email' => $relEmail,
            'rel_action' => $relAction,
        ])->first();
    }

    // 회원 가입 후 로그인 시키는 상태인지 검사
    public static function isUserJoin($user)
    {
        $point = Point::where('user_id', $user->id)
                ->whereRaw('date(datetime) = date(CURRENT_DATE())')     // 다음날부터 로그인시 로그인 포인트 받음
                ->orderBy('id', 'desc')
                ->first();

        if(str_contains($point['content'], '회원가입')) {
            return true;
        }

        return false;
    }

    // 포인트 사용을 검사하고 각종 이벤트 후 포인트를 더해 준다.
    public static function addPoint($data)
    {
        // 환경 설정의 포인트 설정에 체크(userPoint == 1)해야 포인트를 부여한다.
        $usePoint = Cache::get("config.homepage")->usePoint;
        if($usePoint != 0) {
            $user = $data['user'];
            // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
            $existPoint = static::checkPoint($data['relTable'], $data['relEmail'], $data['relAction']);

            if(is_null($existPoint)) {
                $pointToGive = static::pointType($data['type']);
                $user->point += $pointToGive;   // 유저 테이블에 포인트 반영
                static::loggingPoint($user, $pointToGive, $data['relTable'], $data['relEmail'], $data['relAction'], $data['content']);     // 포인트 내역 기록
            }

            $user->save();
        }
    }

    // 포인트 테이블에 포인트 부여내역을 기록
    public static function loggingPoint($user, $pointToGive, $relTable, $relEmail ,$relAction, $content)
    {
        Point::create([
            'user_id' => $user->id,
            'datetime' => Carbon::now(),
            'content' => $content,
            'point' => $pointToGive,
            'user_point' => $user->point,
            'expire_date' => date('9999-12-31'),
            'rel_table' => $relTable,
            'rel_email' => $relEmail,
            'rel_action' => $relAction,
        ]);
    }

    // 포인트 삭제
    public function deletePoint($userId, $relTable, $relEmail, $relAction)
    {
        $result = 0;
        if($relTable || $relEmail || $relAction) {
            // 포인트 내역정보
            $point = Point::where([
                'user_id' => $userId,
                'rel_table' => $relTable,
                'rel_email' => $relEmail,
                'rel_action' => $relAction,
            ])->first();

            if( !is_null($point) ) {
                if($point->point < 0) {
                    $userId = $point->user_id;
                    $usePoint = abs($point->point);

                    $this->deleteUsePoint($userId, $usePoint);
                } else {
                    if($point->use_point > 0) {
                        $this->insertUsePoint($userId, $point->use_point, $point->id);
                    }
                }

                $result = Point::where([
                    'user_id' => $userId,
                    'rel_table' => $relTable,
                    'rel_email' => $relEmail,
                    'rel_action' => $relAction,
                ])->delete();

                // user_point에 반영
                Point::where('user_id', $userId)
                    ->where('id', '>', $point->id)
                    ->decrement('user_point', $point->point);

                // 포인트 내역의 합을 구하고
                $sumPoint = $this->sumPointByUser($userId);

                // User의 포인트 업데이트
                $result = User::where('id', $userId)->update(['point' => $sumPoint]);
            }
        }

        return $result;
    }

    // 내역 변경시 포인트 부여
    public function insertPoint($userId, $point, $content='', $relTable='', $relEmail='', $relAction='', $expire=0)
    {
        $configHomepage = Cache::get("config.homepage");
        if(!$configHomepage->usePoint) {
            return 0;
        }
        if($point == 0) {
            return 0;
        }
        if($userId == '') {
            return 0;
        }
        $user = User::where('id', $userId)->first();
        if(is_null($user)) {
            return 0;
        }

        $userPoint = $this->sumPointByUser($userId);

        // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
        $existPoint = static::checkPoint($relTable, $relEmail, $relAction);
        if( !is_null($existPoint) ) {
            return -1;
        }

        // 포인트 건별 생성
        // 만료일 설정
        $expireDate = date('9999-12-31');
        if($configHomepage->pointTerm > 0) {
            if($expire > 0) {
                // $expireDate = date
            } else {
                // $expireDate = date
            }
        }
        $pointExpired = 0;
        if($point < 0) {
            $pointExpired = 1;
            $expireDate = Carbon::now();
        }
        $pointUserPoint = $userPoint + $point;

        Point::create([
                    'user_id' => $userId,
                    'datetime' => Carbon::now(),
                    'content' => $content,
                    'use_point' => 0,
                    'point' => $point,
                    'user_point' => $pointUserPoint,
                    'expired' => $pointExpired,
                    'expire_date' => $expireDate,
                    'rel_table' => $relTable,
                    'rel_email' => $relEmail,
                    'rel_action' => $relAction,
        ]);
        // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
        if($point < 0) {
            $this->insertUsePoint($userId, $point);
        }
        // User 테이블의 point 업데이트
        User::where('id', $userId)->update(['point' => $pointUserPoint]);

        return 1;
    }

    // 유저별 포인트 합 구하기
    private function sumPointByUser($userId)
    {
        // 만료된 포인트 내역 처리

        // 포인트 합
        return Point::where('user_id', $userId)->sum('point');
    }

    // 사용포인트 삭제
    public function deleteUsePoint($userId, $usePoint)
    {
        $point1 = abs($usePoint);

        $points = Point::where('user_id', $userId)
                    ->where('expired', '<>', 1)
                    ->where('use_point', '>', 0)
                    ->get();

        foreach($points as $point) {
            $point2 = $point->use_point;

            $expired = $point->expired;
            if($point->expired == 100 && ($point->expire_date == '9999-12-31' || $point->expire_date >= Carbon::now()) ) {
                $expired = 0;
            }
            if($point2 > $point1) {
                Point::where('id', $point->id)
                    ->decrement('use_point', $point1, ['expired' => $expired]);
                break;
            } else {
                Point::where('id', $point->id)
                    ->update([
                        'use_point' => 0,
                        'expired' => $expired,
                    ]);
                $point1 -= $point2;
            }
        }
    }

    // 사용포인트 입력
    public function insertUsePoint($userId, $usePoint, $id='')
    {
        $point1 = abs($usePoint);

        $points = Point::where('user_id', $userId)
                    ->where('id', '<>', $id)
                    ->where('expired', '=', 0)
                    ->where('use_point', '>', 0)
                    ->get();

        foreach($points as $point) {
            $point2 = $point->point;
            $point3 = $point->use_point;

            if(($point2 - $point3) > $point) {
                Point::where('id', $id)->increment('use_point', $point1);
                break;
            } else {
                $point4 = $point2 - $point3;
                Point::where('id', $id)->increment('use_point', $point4, ['expired' => 100]);
                $point1 -= $point4;
            }
        }
    }

    // 글 삭제 - 포인트 삭제
    public function deleteWritePoint($writeModel, $boardId, $writeId)
    {
       $write = $writeModel->find($writeId);
       $board = Board::find($boardId);
       // 원글에서의 처리
       $deleteResult = 0;
       $insertResult = 0;
       if(!$write->is_comment) {
           // 포인트 삭제 및 사용 포인트 다시 부여
           $deleteResult = $this->deletePoint($write->user_id, $board->table_name, $writeId, '쓰기');
           if($deleteResult == 0) {
               $insertResult = $this->insertPoint($write->user_id, $board->write_point * (-1), $board->subject. ' '. $writeId. ' 글삭제');
           }
       } else {   // 댓글에서의 처리
           // 포인트 삭제 및 사용 포인트 다시 부여
           $deleteResult = $this->deletePoint($write->user_id, $board->table_name, $writeId, '댓글');
           if($deleteResult == 0) {
               $insertResult = $this->insertPoint($write->user_id, $board->write_point * (-1), $board->subject. ' '. $write->parent. '-'. $writeId. ' 댓글삭제');
           }
       }
    }

}
