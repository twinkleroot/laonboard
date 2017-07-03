<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Common\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SimpleController extends Controller
{

    // phpinfo()
    public function phpinfo()
    {
        $menuCode = ['100800', 'r'];
        if(auth()->user()->isSuperAdmin() || Gate::allows('view-admin-mailtest', Util::getManageAuthModel($menuCode))) {
            return view('admin.phpinfo');
        } else {
            return view('message', [
                'message' => '최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.',
                'redirect' => '/admin/index'
            ]);
        }
    }

    // 부가서비스
    public function extraService()
    {
        $menuCode = ['100810', 'r'];
        if(auth()->user()->isSuperAdmin() || Gate::allows('view-admin-mailtest', Util::getManageAuthModel($menuCode))) {
            return view('admin.extra_service');
        } else {
            return view('message', [
                'message' => '최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.',
                'redirect' => '/admin/index'
            ]);
        }
    }
}
