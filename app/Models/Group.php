<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;
use Carbon\Carbon;

class Group extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function __construct()
    {
        $this->table = 'groups';
    }

    // 회원 모델과의 관계 설정
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')->withPivot('id', 'created_at');
    }

    // 게시판 모델과의 관계 설정
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    // 게시판그룹 게시판
    public function getGroupContents($groupId, $skin, $default)
    {
        $skin = view()->exists("latest.$skin.index") ? $skin : $default;
        $group = Group::where('group_id', $groupId)->first();

        $boards =
            $group->boards()->where([
                'group_id' => $group->id,
                'use_cert' => 'not-use',
            ])
            // ->where('device', '<>', 'mobile')
            ->where('list_level', '<=' , auth()->guest() ? 1 : auth()->user()->level)   // guest는 회원 레벨 1
            ->orderBy('order')
            ->get();

        $boards = getLatestWrites($boards, 5, 70);

        return [
            'latests' => $boards,
            'skin' => $skin,
            'groupName' => is_null($group) ? '' : $group->subject,
        ];
    }

    // index 페이지에서 필요한 파라미터 가져오기
    public function getGroupIndexParams($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';

        $query =
            Group::select('groups.*',
                    DB::raw('
                        ( select count(gu.id)
                          from '. env('DB_PREFIX'). 'group_user as gu
                          where gu.group_id = '. env('DB_PREFIX'). 'groups.id
                        ) as count_users,
                        ( select count(b.id)
                          from '. env('DB_PREFIX'). 'boards as b
                          where b.group_id = '. env('DB_PREFIX'). 'groups.id
                        ) as count_board'
                    )
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
            $query = $query->orderBy('order')->orderBy('group_id');
        }

        $groups = $query->paginate(cache('config.homepage')->pageRows);

        $queryString = "?kind=$kind&keyword=$keyword&page=". $groups->currentPage();

        return [
            'groups' => $groups,
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
            'queryString' => $queryString,
        ];

    }

    // 그룹 아이디가 존재하는지 확인
    public function existGroupId($request)
    {
        $group = Group::where(['group_id' => $request->get('group_id')])->first();
        if($group) {
            return true;
        }
        return false;
    }

    // create 페이지에서 필요한 파라미터 가져오기
    public function getGroupCreateParams()
    {
        return [
            'action' => route('admin.groups.store'),
            'type' => 'create',
        ];
    }

    // 추가한 게시판 그룹 저장
    public function storeGroup($data)
    {
        $data = array_except($data, ['_token']);

        $data = exceptNullData($data);
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        if(Group::insertGetId($data)) {
            return $data['group_id'];
        }

        return 0;

    }

    // edit 페이지에서 필요한 파라미터 가져오기
    public function getGroupEditParams($group)
    {
        $group->count_users = $group->users->count();
        // $group->count_users = GroupUser::where('group_id', $group->id)->count();

        return [
            'group' => $group,
            'action' => route('admin.groups.update', $group->id),
            'type' => 'edit',
        ];
    }

    // 수정
    public function updateGroup($data, $id)
    {
        $data = array_except($data, ['_token']);
        $data = exceptNullData($data);

        $group = Group::findOrFail($id);

        if($group->update($data)) {
            return $group->subject;
        } else {
            return false;
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
        // $deviceArr = explode(',', $request->get('devices'));

        $index = 0;
        foreach($idArr as $id) {
            $group = Group::find($id);

            if(!is_null($group)) {
                $group->update([
                    'subject' => $subjectArr[$index],
                    'admin' => $adminArr[$index],
                    'order' => $orderArr[$index],
                    'use_access' => $useAccessArr[$index] == '1' ? 1 : 0,
                    // 'device' => $deviceArr[$index],
                ]);
                $index++;
            } else {
                abort('500', '정보를 수정할 게시판 그룹이 존재하지 않습니다. 게시판 그룹이 잘 선택 되었는지 확인해 주세요.');
            }
        }
    }

    // 그룹 선택 삭제
    public function deleteGroups($ids)
    {
        $deleteIdArr = explode(',', $ids);
        foreach($deleteIdArr as $id) {
            if(!Board::where('group_id', $id)->first()) {
                Group::destroy($id);
            } else {
                return '이 그룹에 속한 게시판이 존재하여 게시판 그룹을 삭제할 수 없습니다. 이 그룹에 속한 게시판을 먼저 삭제하여 주십시오.';
            }
        }
    }
}
