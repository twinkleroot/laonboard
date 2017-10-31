<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Theme;

class ThemesController extends Controller
{
    public $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    public function index()
    {
        $params = $this->theme->getIndexParams();

        return view('admin.themes.index', $params);
    }

    // 테마 변경
    public function update(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $this->theme->updateTheme($request);
    }

    // 개별 스킨 변경
    public function updateSkins(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $this->theme->updateSkins($request);

        return redirect()->back();
    }

    // 테마 상세 보기
    public function detail(Request $request)
    {
        $params = $this->theme->getDetailParams($request);

        return view('admin.themes.detail', $params);
    }
}
