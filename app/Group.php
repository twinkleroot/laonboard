<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;
use App\User;
use App\Board;
use App\Common\Util;

class Group extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $rules = [
        'group_id' => 'required|regex:/^[a-zA-Z0-9_]+$/',
        'subject' => 'required',
    ];

    // 회원 모델과의 관계 설정
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('id', 'created_at');
    }

    // 게시판 모델과의 관계 설정
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    // index 페이지에서 필요한 파라미터 가져오기
    public function getGroupIndexParams($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';

        $query =
            DB::table('groups as g')
            ->selectRaw('
                        g.id,
                        g.group_id,
                        g.subject,
                        g.admin,
                        g.use_access,
                        g.order,
                        g.device,
                        g.created_at,
                        (   select count(gu.id)
                            from group_user as gu
                            where gu.group_id = g.id
                        ) as count_users,
                        (   select count(b.id)
                            from boards as b
                            where b.group_id = g.id
                        ) as count_board'
            );

        // 최고 관리자가 아닐때
        if( !auth()->user()->isSuperAdmin() ) {
            $query = $query->where('admin', auth()->user()->email);
        }

        // 검색 추가
        if($kind) {
            $query = $query->where($kind, 'like', '%'. $keyword. '%');
        }

        // 정렬 추가
        if($order) {
            $query = $query->orderBy($order, $direction);
        } else {
            $query = $query->orderBy('g.group_id');
        }
        
        $groups = $query->paginate(Cache::get('config.homepage')->pageRows);

        return [
            'groups' => $groups,
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
        ];

    }

    // 그룹 아이디가 존재하는지 확인
    public function existGroupId($request)
    {
        $group = Group::where(['group_id' => $request->get('group_id')])->first();
        if(!is_null($group)) {
            return true;
        }
        return false;
    }

    // 추가한 게시판 그룹 저장
    public function store($data)
    {
        $data = array_except($data, ['_token']);

        $data = Util::exceptNullData($data);

        return Group::create($data);

    }

    // 그룹 선택 삭제
    public function deleteGroups($ids)
    {
        $result = Group::whereRaw('id in (' . $ids . ') ')->delete();
        if($result > 0) {
            return '선택한 게시판 그룹이 삭제되었습니다.';
        } else {
            return '선택한 게시판 그룹의 삭제가 실패하였습니다.';
        }
    }

    // 그룹 선택 수정
    public function selectedUpdate($request)
    {
        $idArr = explode(',', $request->get('ids'));
        $subjectArr = explode(',', $request->get('subjects'));
        $adminArr = explode(',', $request->get('admins'));
        $orderArr = explode(',', $request->get('orders'));
        $useAccessArr = explode(',', $request->get('use_accesss'));
        $deviceArr = explode(',', $request->get('devices'));

        $index = 0;
        foreach($idArr as $id) {
            $group = Group::find($id);

            if(!is_null($group)) {
                $group->update([
                    'subject' => $subjectArr[$index],
                    'admin' => $adminArr[$index],
                    'order' => $orderArr[$index],
                    'use_access' => $useAccessArr[$index] == '1' ? 1 : 0,
                    'device' => $deviceArr[$index],
                ]);
                $index++;
            } else {
                abort('500', '정보를 수정할 게시판 그룹이 존재하지 않습니다. 게시판 그룹이 잘 선택 되었는지 확인해 주세요.');
            }
        }
    }

    // create 페이지에서 필요한 파라미터 가져오기
    public function getGroupCreateParams()
    {
        return [
            'config' => Cache::get("config.homepage"),
            'title' => '생성',
            'action' => route('admin.groups.store'),
            'type' => 'create',
        ];
    }

    // edit 페이지에서 필요한 파라미터 가져오기
    public function getGroupEditParams($id)
    {
        return [
            'config' => Cache::get("config.homepage"),
            'group' => Group::findOrFail($id),
            'title' => '수정',
            'action' => route('admin.groups.update', $id),
            'type' => 'edit',
        ];
    }

    // 수정
    public function updateGroup($data, $id)
    {

        $data = array_except($data, ['_token']);
        $data = Util::exceptNullData($data);

        $group = Group::findOrFail($id);

        if($group->update($data)) {
            return $group->subject;
        } else {
            return false;
        }
    }

}
