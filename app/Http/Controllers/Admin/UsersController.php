<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin\Config;
use App\Admin\AdminUser;

class UsersController extends Controller
{
    public $userModel;

    public function __construct(AdminUser $userModel)
    {
        $this->userModel = $userModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->userModel)) {
            abort(403, '회원 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->userModel->userList($request);

        return view('admin.users.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->cant('create', AdminUser::class)) {
            abort(403, '회원 추가에 대한 권한이 없습니다.');
        }

        return view('admin.users.create', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->cant('create', AdminUser::class)) {
            abort(403, '회원 추가에 대한 권한이 없습니다.');
        }

        $adminConfig = new Config();
        $rulePassword = $adminConfig->getPasswordRuleByConfigPolicy();
        $rule = [
            'email' => 'required|email|max:255|unique:users',
            'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
            'password' => $rulePassword[0] . '|' . $rulePassword[2],
        ];

        $this->validate($request, $rule);

        $id = $this->userModel->addUser($request);

        return redirect(route('admin.users.edit', $id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

        $user = getUser($id);
        if(!$user) {
			return alertRedirect('존재하지 않는 회원입니다.', '/admin/index');
        }

		$params = $this->userModel->editParams($user, $id);

        return view('admin.users.edit', $params);
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
        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

		$this->userModel->updateUserInfo($request, $id);

        return redirect()->back();
    }

    /**
    *  선택 수정 기능
    */
    public function selectedUpdate(Request $request)
    {
        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

        $this->userModel->selectedUpdate($request);

        return redirect(route('admin.users.index'))->with('message', '선택한 회원정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (auth()->user()->cant('delete', $this->userModel)) {
            abort(403, '회원 삭제에 대한 권한이 없습니다.');
        }

		$this->userModel->deleteUser($request);

        return redirect(route('admin.users.index'))->with('message', '선택한 회원이 삭제되었습니다.');
    }
}
