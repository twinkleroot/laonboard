<?php

namespace Modules\Inform\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Inform\Models\Inform;
use Gate;

class InformController extends Controller
{
    public $inform;

    public function __construct(Inform $inform)
    {
        $this->inform = $inform;
    }

    // 회원 알림 내역
    public function index(Request $request)
    {
        $params = [
            'informs' => $this->inform->getInforms($request)
        ];

        return view("modules.inform.index", $params);
    }

    // 회원 알림 읽음 표시 (읽음 표시 버튼 클릭)
    public function markAsRead(Request $request)
    {
        $this->inform->markAsReadInforms($request->ids);

        return redirect()->back();
    }

    // 회원 알림 내역 삭제
    public function destroy(Request $request)
    {
        $this->inform->destroyInforms($request);

        return redirect()->back();
    }

    // 회원 알림 읽음 표시 (ajax)
    public function markAsReadOne(Request $request)
    {
        return $this->inform->markAsReadOne($request->id);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function adminIndex()
    {
        $menuCode = ['inform', 'r'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-inform-index', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        return view('modules.inform.admin.index');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function adminUpdate(Request $request)
    {
        $menuCode = ['inform', 'w'];
        if(!auth()->user()->isSuperAdmin() && !Gate::allows('module-inform-update', getManageAuthModel($menuCode))) {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }

        $message = $this->inform->updateAdmin($request);

        return redirect()->back()->with('message', $message);
    }

}
