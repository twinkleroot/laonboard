<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Group;

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

        return view("admin.groups.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', Group::class)) {
            abort(403, '최고관리자만 접근 가능합니다.');
        }

        $params = $this->groupModel->getGroupCreateParams();

        return view("admin.groups.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', Group::class)) {
            abort(403, '최고관리자만 접근 가능합니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        if($this->groupModel->existGroupId($request)) {
            return redirect(route('admin.groups.create'))->with('message', '이미 존재하는 그룹 ID입니다.');
        }

        $groupId = $this->groupModel->storeGroup($request->all());
        if($groupId) {
            return redirect(route('admin.groups.edit', $groupId))->with('message', $request->get('subject') . '게시판 그룹을 생성하였습니다.');
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
        $group = Group::whereGroupId($id)->first();
        if (auth()->user()->cant('update', $group)) {
            abort(403, '해당 게시판 그룹 수정에 대한 권한이 없습니다.');
        }

        $params = $this->groupModel->getGroupEditParams($group);

        return view("admin.groups.form", $params);
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
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('update', $this->groupModel->find($id))) {
            abort(403, '해당 게시판 그룹 수정에 대한 권한이 없습니다.');
        }

        $beforeGroupInfo = Group::find($id);
        $rules = $this->rules();
        $rules = array_except($rules, 'group_id');
        if($beforeGroupInfo->subject == $request->subject) {
            $rules = array_except($rules, 'subject');
        }
        if($beforeGroupInfo->admin == $request->admin) {
            $rules = array_except($rules, 'admin');
        }
        $this->validate($request, $rules, $this->messages());

        $subject = $this->groupModel->updateGroup($request->all(), $id);

        if(!$subject) {
            abort('500', '게시판 그룹 정보의 수정에 실패하였습니다.');
        }

        return redirect(route('admin.groups.edit', $beforeGroupInfo->group_id))->with('message', $subject . '의 게시판 그룹 정보가 수정되었습니다.');
    }

    // 선택 수정 수행
    public function selectedUpdate(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

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
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $deleteList = $this->groupModel->whereIn('id', explode(',', $id))->get();
        foreach($deleteList as $delete) {
            if (auth()->user()->cant('delete', $delete)) {
                abort(403, '해당 게시판 그룹 삭제에 대한 권한이 없습니다.');
            }
        }

        $message = $this->groupModel->deleteGroups($request->get('ids'));

        return redirect(route('admin.groups.index'))->with('message', $message);
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'group_id' => 'bail|required|regex:/^[a-zA-Z0-9_]+$/|unique:groups',
            'subject' => 'bail|required|alpha_dash',
            'admin' => 'bail|email|nullable',
            'use_access' => 'bail|numeric|nullable',
        ];
    }

    public function messages()
    {
        return [
            'group_id.required' => '그룹 ID를 입력해 주세요.',
            'group_id.regex' => '그룹 ID에는 영문자, 숫자, 언더스코어(_)만 들어갈 수 있습니다.',
            'group_id.unique' => '이미 등록된 그룹 ID입니다. 다른 그룹 ID를 입력해 주세요.',
            'subject.required' => '그룹 제목을 입력해 주세요.',
            'subject.alpha_dash' => '그룹 제목에는 영문자, 한글, 숫자, 대쉬(-), 언더스코어(_)만 들어갈 수 있습니다.',
            'admin.email' => '그룹관리자에 올바른 Email양식으로 입력해 주세요.',
            'use_access.numeric' => '접근회원사용에는 숫자만 들어갈 수 있습니다.',
        ];
    }
}
