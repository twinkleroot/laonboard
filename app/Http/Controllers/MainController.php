<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    // 홈페이지 메인
    public function index(Request $request)
    {
        $theme = cache('config.theme')->name ? : 'default';

        return viewDefault("$theme.main");
    }

}
