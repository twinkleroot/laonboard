<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GroupUser extends Model
{
    protected $dates = ['created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'group_id', 'created_at'];

    public $table = 'group_user';

    public $timestamps = false;

    // 접근 가능 그룹 목록을 표시한다.
    public function showAccessibleGroups($id)
    {
        $user = User::find($id);
        $groups = $user->groups;
        $accessible_groups = Group::where([
            'use_access' => 1,
        ])->get();

        return [
            'groups' => $groups,
            'user' => $user,
            'accessible_groups' => $accessible_groups
        ];
    }

    // 접근 가능 그룹을 추가한다.
    public function addAccessibleGroups($request)
    {
        $data = $request->all();
        $groupUser = GroupUser::where([
            'group_id' => $data['group_id'],
            'user_id' => $data['user_id']
        ])->first();
        if(is_null($groupUser)) {
            $addedGroupUser = GroupUser::create([
                'group_id' => $data['group_id'],
                'user_id' => $data['user_id'],
                'created_at' => Carbon::now(),
            ]);
            if(!is_null($addedGroupUser)) {
                return '접근 가능 그룹이 추가되었습니다.';
            } else {
                return '접근 가능 그룹을 추가하는데 실패하였습니다.';
            }
        } else {
            return '이미 등록되어 있는 자료입니다.';
        }
    }

    public function delAccessibleGroups($request)
    {
        $ids = $request->get('ids');
        $result = GroupUser::whereRaw('id in (' . $ids . ') ')->delete();

        if($result > 0) {
            return '선택한 접근 가능 그룹을 삭제하였습니다.';
        } else {
            return '삭제에 실패하였습니다.';
        }
    }
}
