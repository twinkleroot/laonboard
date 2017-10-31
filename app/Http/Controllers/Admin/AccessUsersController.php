<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GroupUser;

// 그룹 접근 가능 회원
class AccessUsersController extends Controller
{
    public $groupUserModel;

    public function __construct(GroupUser $groupUser)
    {
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
        if (auth()->user()->cant('index', $this->groupUserModel)) {
            abort(403, '접근 가능 회원 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->groupUserModel->getAccessibleUsers($id, $request);

        return view("admin.groups.accessUsers", $params);
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
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->groupUserModel)) {
            abort(403, '접근 가능 회원 삭제에 대한 권한이 없습니다.');
        }

        $message = $this->groupUserModel->delAccessibleGroups($request);
        return redirect(route('admin.accessUsers.show', $id))->with('message', $message);
    }
}
