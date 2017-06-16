<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Carbon\Carbon;
use App\Common\Util;
use App\User;
use App\Point;

class Memo extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public $timestamps = false;

    // 메모 목록 뷰에 필요한 파라미터
    public function getIndexParams($request)
    {
        $kind = isset($request->kind) && $request->kind ? $request->kind : 'recv';
        $unkind = $kind == 'recv' ? 'send' : 'recv';
        $countMemo = Memo::where($kind.'_user_id', auth()->user()->id)->count();

        $memos = Memo::selectRaw('memos.*, users.id_hashkey as user_id_hashkey, users.nick, users.email')
            ->leftJoin('users', 'users.id', '=', 'memos.'.$unkind.'_user_id')
            ->where('memos.'.$kind.'_user_id', auth()->user()->id)
            ->orderBy('memos.id', 'desc')
            ->get();

        return [
            'kind' => $kind,
            'countMemo' => $countMemo,
            'memos' => $memos
        ];
    }

    // 메모 쓰기 뷰에 필요한 검사와 파라미터
    public function getCreateParams($request)
    {
        $user = auth()->user();
        if( !$user->open
            && $user->email != Cache::get('config.homepage')->superAdmin
            && $user->id_hashkey != $request->to) {
                return ["message" => "자신의 정보를 공개하지 않으면 다른분에게 쪽지를 보낼 수 없습니다. 정보공개 설정은 회원정보수정에서 하실 수 있습니다."];
        }

        if( isset($request->to) ) {
            $toUser = User::where('id_hashkey', $request->to)->first();

            if( is_null($toUser)) {
                return ["message" => "회원정보가 존재하지 않습니다.\\n\\n탈퇴하였을 수 있습니다."];
            }
            if( !$toUser->open
                && $user->email != Cache::get('config.homepage')->superAdmin) {
                    return ["message" => "정보공개를 하지 않았습니다."];
            }

            $memo = Memo::where('id', $request->id)
                    ->whereRaw('recv_user_id = '. $user->id. ' or send_user_id = '. $user->id)
                    ->first();
            $content = '';
            if($memo) {
                $content = "\n >\n >\n >"
                            .str_replace("\n", "\n> ", Util::getText($memo->memo, 0))
                          ."\n > >";
            }

            return [
                'content' => $content,
                'to' => $toUser->nick
            ];
        }
    }

    // 쪽지 전송 모든 과정
    public function storeMemo($request)
    {
        $allList = explode(',', $request->recv_nicks);
        $allList = array_map('trim', $allList);
        $toList = [];
        $errorList = [];
        $currentUser = auth()->user();

        foreach($allList as $to) {
            $user = User::where('nick', $to)->first();
            if($user) {
                if(session()->get('admin') || ($user->open && (!$user->leave_date || !$user->intercept_date))) {
                    $toList[] = $user;
                } else {
                    $errorList[] = $to;
                }
            } else {
                $errorList[] = $to;
            }
        }

        // 탈퇴, 차단 회원, 정보공개 안한 회원 등에게는 쪽지를 전송하지 않는다.
        $message = $this->checkErrorList($errorList);
        if($message) {
            return $message;
        }
        // 쪽지보낼 때 차감되는 포인트 만큼 보유하고 있는지 확인한다.
        $message = $this->checkPoint($currentUser, $toList);
        if($message) {
            return $message;
        }
        // 쪽지 전송
        $message = $this->sendMemo($currentUser, $toList, $request);
        if($message) {
            return $message;
        }
    }

    // 탈퇴, 차단 회원, 정보공개 안한 회원 등에게는 쪽지를 전송하지 않는다.
    private function checkErrorList($errorList)
    {
        $errorMsg = implode(',', $errorList);
        if($errorMsg && !session()->get('admin')) {
            return "회원닉네임 (". $errorMsg. ") 은(는) 존재(또는 정보공개)하지 않는 회원닉네임 이거나 탈퇴, 접근차단된 회원닉네임 입니다.\\n쪽지를 발송하지 않았습니다.";
        }
    }

    // 쪽지보낼 때 차감되는 포인트 만큼 보유하고 있는지 확인한다.
    private function checkPoint($currentUser, $toList)
    {
        if(!session()->get('admin')) {
            if($toList) {
                $point = Cache::get('config.homepage')->memoSendPoint * count($toList);
                if($point && ($currentUser->point - $point < 0)) {
                    return "보유하신 포인트(". number_format($currentUser->point). "점)가 모자라서 쪽지를 보낼 수 없습니다.";
                }
            }
        }
    }

    // 쪽지 전송
    private function sendMemo($currentUser, $toList, $request)
    {
        $nickList = [];
        foreach($toList as $to) {
            // 쪽지 insert
            Memo::Create([
                'recv_user_id' => $to->id,
                'send_user_id' => $currentUser->id,
                'send_timestamp' => Carbon::now(),
                'memo' => $request->memo
            ]);
            // 실시간 쪽지 알림 기능
            User::where('id', $to->id)->update([
                'memo_call' => $currentUser->id
            ]);
            // 포인트 차감
            if(!session()->get('admin')) {
                $point = new Point();
                $memoSendPoint = (int)Cache::get('config.homepage')->memoSendPoint * -1;
                $content = $to->nick.'('.$to->email.')님께 쪽지 발송';
                $relTable = '@memo';
                $relEmail = $to->id;
                $relAction = Memo::max('id');
                $point->insertPoint($currentUser->id, $memoSendPoint, $content, $relTable, $relEmail, $relAction);
            }

            // 결과 메세지 출력용 쪽지 전송 대상의 닉네임 배열
            $nickList[] = $to->nick;
        }
        // 결과 메세지
        $strNickList = implode(',', $nickList);
        return $strNickList. ' 님께 쪽지를 전달하였습니다.';
    }

    // 메모 읽기
    public function getShowParams($id, $request)
    {
        $kind = $request->kind;
        if($kind != 'recv' && $kind != 'send') {
            return [ 'message' => 'kind 값이 제대로 넘어오지 않았습니다.'];
        } else if($kind == 'recv') { // 쪽지 읽은 시간 기록
            $this->writeReadTime($id);
        }
        $unkind = ($kind == 'recv') ? 'send' : 'recv';

        $memo = Memo::selectRaw('memos.*, users.id_hashkey as user_id_hashkey, users.nick, users.email')
            ->leftJoin('users', 'users.id', '=', 'memos.'.$unkind.'_user_id')
            ->where([
                'memos.id' => $id,
                'memos.'.$kind.'_user_id' => auth()->user()->id
            ])->first();

        $prevMemo = $this->getMemo('prev', $kind, $id);
        $nextMemo = $this->getMemo('next', $kind, $id);

        return [
            'memo' => $memo,
            'kind' => $kind,
            'prevMemo' => $prevMemo,
            'nextMemo' => $nextMemo
        ];
    }

    // 쪽지 읽은 시간 기록
    private function writeReadTime($id)
    {
        Memo::where([
            'id' => $id,
            'recv_user_id' => auth()->user()->id,
            'read_timestamp' => null
        ])->update([
            'read_timestamp' => Carbon::now()
        ]);
    }

    // 이전, 다음 메모 가져오기
    private function getMemo($type, $kind, $id)
    {
        $operator = ($type == 'prev') ? '>' : '<';
        $sort = ($type == 'prev') ? 'asc' : 'desc';

        $memo = Memo::where('id', $operator, $id)
            ->where($kind. '_user_id', auth()->user()->id)
            ->orderBy('id', $sort)
            ->first();

        $id = 0;
        if($memo) {
            $id = $memo->id;
        }

        return $id;
    }

    // 메모 삭제
    public function deleteMemo($id)
    {
        $memo = Memo::find($id);
        // 쪽지를 읽기 전에 삭제할 경우 실시간 알림 해제
        if( !$memo->read_timestamp ) {
            User::where([
                'id' => $memo->recv_user_id,
                'memo_call' => $memo->send_user_id
            ])->update([
                'memo_call' => ''
            ]);
        }
        // 쪽지 삭제
        return $memo->delete();
    }
}
