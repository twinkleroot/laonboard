<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'group_id', 'subject', 'admin', 'use_access', 'order', 'device',
    ];

    public $rules = [
        'group_id' => 'required|regex:/^[a-zA-Z0-9_]+$/',
        'subject' => 'required',
    ];

    // 모든 그룹 가져오기
    public function allGroup()
    {
        return Group::orderBy('id', 'desc')->get();
    }

    public function existGroupId($request)
    {
        $group = Group::where(['group_id' => $request->get('group_id')])->first();
        if(!is_null($group)) {
            return true;
        }
        return false;
    }

    // 추가한 게시판 그룹 저장
    public function store($request)
    {
        $groupInfo = [
            'group_id' => $request->get('group_id'),
            'subject' => $request->get('subject'),
            'device' => $request->get('device'),
            'admin' => $request->get('admin'),
            'use_access' => $request->has('use_access') ? $request->get('use_access') : 0,
        ];

        return Group::create($groupInfo);
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

    public function findGroup($id)
    {
        return Group::findOrFail($id);
    }

    public function groupInfoUpdate($request, $id)
    {
        $group = Group::findOrFail($id);
        $data = $request->all();

        return $group->update([
            'group_id' => $data['group_id'],
            'subject' => $data['subject'],
            'device' => $data['device'],
            'admin' => $data['admin'],
            'use_access' => $data['use_access'],
        ]);


    }

}
