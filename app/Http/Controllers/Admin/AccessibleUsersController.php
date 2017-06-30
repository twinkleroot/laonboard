<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupUser;

// 그룹 접근 가능 회원
class AccessibleUsersController extends Controller
{
    public $groupUserModel;

    public function __construct(GroupUser $groupUser)
    {
        $this->middleware('admin');

        $this->groupUserModel = $groupUser;
    }

    /**
     * Display the specified resource.
     * 목록 보여주기
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $params = $this->groupUserModel->getAccessibleUsers($id, $request);

        return view('admin.group_user.accessible_user_list', $params);
    }

    /**
     * Remove the specified resource from storage.
     * 선택 삭제
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $message = $this->groupUserModel->delAccessibleGroups($request);
        return redirect(route('admin.accessUsers.show', $id))
            ->with('message', $message);
    }
}
