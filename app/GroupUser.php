<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\Config;

class GroupUser extends Model
{
    protected $dates = ['created_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $table = 'group_user';

    public $timestamps = false;

    // 접근 가능 그룹 목록을 표시한다.
    public function getAccessibleGroups($id)
    {
        $user = User::find($id);
        $groups = $user->groups;
        $accessible_groups = Group::where([
            'use_access' => 1,
        ])->get();

        return [
            'config' => Config::getConfig('config.homepage'),
            'groups' => $groups,
            'user' => $user,
            'accessible_groups' => $accessible_groups,
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

    // 접근 가능 그룹, 회원 선택 삭제
    public function delAccessibleGroups($request)
    {
        $ids = $request->get('ids');
        $result = GroupUser::whereRaw('id in (' . $ids . ') ')->delete();

        if($result > 0) {
            return '선택한 접근 가능 그룹(회원)을 삭제하였습니다.';
        } else {
            return '삭제에 실패하였습니다.';
        }
    }

    // 접근 가능 회원 목록을 표시한다.
    public function getAccessibleUsers($id)
    {
        $config = Config::getConfig('config.homepage');
        $group = Group::find($id);
        $users = $group->users()
                ->select(DB::raw('users.*, count.count_groups'))
                ->leftJoin(
                    DB::raw('(select users.id as id, count(group_user.id) as count_groups
                	from group_user
                	left join users
                	on group_user.user_id = users.id
                	group by users.id) as count'),
                    'users.id', '=', 'count.id'
                )
                ->paginate($config->pageRows);

        return [
            'config' => $config,
            'group' => $group,
            'users' => $users,
            'keyword' => ''
        ];
    }
}
