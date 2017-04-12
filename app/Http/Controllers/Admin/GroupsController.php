<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Group;

class GroupsController extends Controller
{
    public $groupModel;

    public function __construct(Group $groupModel)
    {
        $this->middleware('level:10');

        $this->groupModel = $groupModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = $this->groupModel->getGroupIndexParams();

        return view('admin.groups.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $this->validate($request, $this->groupModel->rules);

        if($this->groupModel->existGroupId($request)) {
            return redirect(route('admin.groups.create'))->with('message', '이미 존재하는 그룹 ID입니다.');
        }

        $group = $this->groupModel->store($request->all());

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
        $subject = $this->groupModel->updateGroup($request->all(), $id);

        if(!$subject) {
            abort('500', '게시판 그룹 정보의 변경에 실패하였습니다.');
        }

        return redirect(route('admin.groups.index'))->with('message', $subject . '의 게시판 그룹 정보가 수정되었습니다.');
    }

    // 선택 수정 수행
    public function selectedUpdate(Request $request)
    {
        if($this->groupModel->existGroupId($request)) {
            return redirect(route('admin.groups.edit'))->with('message', '이미 존재하는 그룹 ID입니다.');
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
        $message = $this->groupModel->deleteGroups($request->get('ids'));

        return redirect(route('admin.groups.index'))->with('message', $message);
    }
}
