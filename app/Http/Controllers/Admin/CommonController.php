<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Group;

class CommonController extends Controller
{
    // 관리자 검색 기능
    public function search(Request $request)
    {
        $param = $request->all();
        $searchData = [];
        $view = '';

        switch ($param['admin_page']) {
            case 'boardGroup':
                $searchData = [
                    'groups' => Group::where($param['kind'], 'like', '%'.$param['keyword'].'%')->get()
                ];
                $view = 'admin.groups.index';
                break;
            case '':
                // case 추가에 따라 사용하는 모델도 추가해야 한다.
                break;
            default:
                # code...
                break;
        }

        return view($view, $searchData);
    }
}
