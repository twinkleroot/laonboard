<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;
use Gate;
use File;

class ExtrasController extends Controller
{
    // 세션파일 일괄삭제
    public function deleteSession() {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $results = [];
        $sessionAll = session()->all();
        foreach($sessionAll as $key => $value) {
            if(substr($key, 0, 8) == 'session_') {
                $result[] = $key;
            }
        }

        return view("admin.configs.session_delete", [ 'sessions' => $results ]);
    }

    // 최신글 캐시파일 일괄삭제
    public function deleteCache() {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $boards = Board::select('boards.*', 'groups.id as group_id', 'groups.subject as group_subject', 'groups.order as group_order')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            // ->where('boards.device', '<>', 'mobile')
            ->where('boards.use_cert', 'not-use')
            ->orderBy('groups.order')
            ->orderBy('boards.order')
            ->get();

        $results = [];
        foreach($boards as $board) {
            $cacheName = 'main-'. $board->table_name;
            if(cache()->has($cacheName)) {
                $results[] = $cacheName;
            }
        }

        return view("admin.configs.cache_delete", [ 'caches' => $results ]);
    }

    // 썸네일 파일 일괄삭제
    public function deleteThumbnail()
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $path = storage_path('app/public');
        $directories = File::directories($path);
        $files = [];
        foreach($directories as $dir) {
            $files[] = File::files($dir);
        }
        $files = array_flatten($files);
        $results = [];
        foreach($files as $file) {
            $baseFileName = basename($file);
            if(substr($baseFileName, 0, 6) == 'thumb-') {
                $results[] = $file;
            }
        }

        return view("admin.configs.thumbnail_delete", [ 'files' => $results ]);
    }

    // phpinfo()
    public function phpinfo()
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $menuCode = ['100800', 'r'];
        if(auth()->user()->isSuperAdmin() || Gate::allows('view-admin-phpinfo', getManageAuthModel($menuCode))) {
            return view('admin.configs.phpinfo');
        } else {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }
    }

    // 부가서비스
    public function extraService()
    {
        $menuCode = ['100810', 'r'];
        if(auth()->user()->isSuperAdmin() || Gate::allows('view-admin-extra_service', getManageAuthModel($menuCode))) {
            return view("admin.configs.extra_service");
        } else {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }
    }
}
