<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Group;

class GroupsController extends Controller
{
    public $groupModel;

    public function __construct(Group $groupModel)
    {
        $this->groupModel = $groupModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->groupModel)) {
            abort(403, '게시판 그룹 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->groupModel->getGroupIndexParams($request);

        return view('admin.groups.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->cant('create', Group::class)) {
            abort(403, '최고관리자만 접근 가능합니다.');
        }

        $params = $this->groupModel->getGroupCreateParams();

        return view('admin.groups.form', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->cant('create', Group::class)) {
            abort(403, '최고관리자만 접근 가능합니다.');
        }

        $rules = [
            'group_id' => 'required|regex:/^[a-zA-Z0-9_]+$/',
            'subject' => 'required',
        ];

        $this->validate($request, $rules);

        if($this->groupModel->existGroupId($request)) {
            return redirect(route('admin.groups.create'))->with('message', '이미 존재하는 그룹 ID입니다.');
        }

        $group = $this->groupModel->storeGroup($request->all());

        if(!is_null($group)) {
            return redirect(route('admin.groups.index'))->with('message', $request->get('subject') . '게시판 그룹을 생성하였습니다.');
        } else {
            return redirect(route('admin.groups.create'))->with('message', $request->get('subject') . '게시판 그룹 생성에 실패하였습니다.');
        }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->cant('update', $this->groupModel->find($id))) {
            abort(403, '해당 게시판 그룹 수정에 대한 권한이 없습니다.');
        }

        $params = $this->groupModel->getGroupEditParams($id);

        return view('admin.groups.form', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->cant('update', $this->groupModel->find($id))) {
            abort(403, '해당 게시판 그룹 수정에 대한 권한이 없습니다.');
        }

        $subject = $this->groupModel->updateGroup($request->all(), $id);

        if(!$subject) {
            abort('500', '게시판 그룹 정보의 수정에 실패하였습니다.');
        }

        return redirect(route('admin.groups.index'))->with('message', $subject . '의 게시판 그룹 정보가 수정되었습니다.');
    }

    // 선택 수정 수행
    public function selectedUpdate(Request $request)
    {
        $idArr = explode(',', $request->get('ids'));
        foreach($idArr as $id) {
            if (auth()->user()->cant('update', $this->groupModel->find($id))) {
                abort(403, '해당 게시판 그룹 수정에 대한 권한이 없습니다.');
            }
        }

        $this->groupModel->selectedUpdate($request);

        return redirect(route('admin.groups.index'))->with('message', '선택한 게시판 그룹 정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $deleteList = $this->groupModel->whereIn('id', explode(',', $id))->get();
        foreach($deleteList as $delete) {
            if (auth()->user()->cant('delete', $delete)) {
                abort(403, '해당 게시판 그룹 삭제에 대한 권한이 없습니다.');
            }
        }

        $message = $this->groupModel->deleteGroups($request->get('ids'));

        return redirect(route('admin.groups.index'))->with('message', $message);
    }
}
