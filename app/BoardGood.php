<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class BoardGood extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 추천, 비추천 처리 로직
    public function good($writeModel, $writeId, $good)
    {
        $write = $writeModel->find($writeId);
        $board = $writeModel->board;
        $user = auth()->user();

        $message = '';
        if(is_null($user)) {
            $message = '회원만 가능합니다.';
        } else if($write->user_id == $user->id) {
            $message = '자신의 글에는 추천 또는 비추천 하실 수 없습니다.';
        } else if(!$board->use_good && $good == 'good') {
            $message = '이 게시판은 추천 기능을 사용하지 않습니다.';
        } else if(!$board->use_nogood && $good == 'nogood') {
            $message = '이 게시판은 비추천 기능을 사용하지 않습니다.';
        }

        if($message != '') {
            return ['error' => $message];
        }

        $boardGood = BoardGood::where([
                        'board_id' => $board->id,
                        'write_id' => $write->id,
                        'user_id' => $user->id,
                    ])->whereIn('flag', ['good', 'nogood'])->first();

        if(!is_null($boardGood)) {
            if ($boardGood->flag == 'good') {
                $status = '추천';
            } else {
                $status = '비추천';
            }

            $message = "이미 $status 하신 글 입니다.";
        } else {
            // 추천 or 비추천 카운트 증가
            $goodValue = $write[$good] + 1;

            DB::table('write_'. $board->table_name)
                ->where('id', $write->id)
                ->update([
                    $good => $goodValue
                ]);
            // 내역 기록
            BoardGood::insert([
                'board_id' => $board->id,
                'write_id' => $write->id,
                'user_id' => $user->id,
                'flag' => $good,
                'created_at' => Carbon::now(),
            ]);

            return ['count' => $goodValue];
        }

        return ['error' => $message];
    }
}
