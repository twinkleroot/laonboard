<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Autosave extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'autosaves';
    }

    // 임시 저장 - 저장
    public function autosave($request)
    {
        Autosave::updateOrCreate([
                'unique_id' => $request->uid
            ], [
                'user_id' => auth()->user()->id,
                'unique_id' => $request->uid,
                'subject' => $request->subject,
                'content' => $request->content,
                'created_at' => Carbon::now(),
            ]);

        return static::getAutosaveCount();
    }

    // 로그인한 사용자로 임시저장 되어 있는 글 갯수를 가져온다.
    public static function getAutosaveCount()
    {
        return Autosave::where('user_id', auth()->user()->id)->count();
    }

    // 임시저장 목록을 불러온다.
    public function autosaveList()
    {
        $lists = Autosave::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        foreach($lists as $list) {
            $list->subject = htmlspecialchars(utf8Strcut($list->subject, 20), ENT_QUOTES);
        }
        return $lists;
    }

    public function autosaveView($id)
    {
        $autosave = Autosave::where([
                        'user_id' => auth()->user()->id,
                        'id'    => $id
                    ])->first();

        return $autosave;
    }

    // 임시저장 목록에서 항목을 삭제한다.
    public function autosaveDelete($id)
    {
        $result = Autosave::where([
                    'user_id' => auth()->user()->id,
                    'id'    => $id
                ])->delete();
        if(!$result) {
            return '-1';
        }

        return static::getAutosaveCount();
    }

}
