<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main;
use Cache;

class MainController extends Controller
{
    public $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    // 홈페이지 메인
    public function index()
    {
        $mainContents = $this->main->getMainContents(Cache::get('config.homepage')->newSkin, 'default');

        return view('main', $mainContents);
    }

    // 게시판 그룹별 메인
    public function groupIndex($groupId)
    {
        $groupContents = $this->main->getGroupContents($groupId, Cache::get('config.homepage')->newSkin, 'default');

        return view('group', $groupContents);
    }
}
