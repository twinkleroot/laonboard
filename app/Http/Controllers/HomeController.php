<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main;

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
        $mainContents = $this->main->getMainContents('default');    // 환경설정에 있는 값으로 가져오도록 고쳐야 함.

        $skin = 'default';   // default를 가리킴, 환경설정에 있는 값으로 가져오도록 고쳐야 함.

        return view($skin, $mainContents);
    }

    // 게시판 그룹별 메인
    public function groupIndex($groupId)
    {
        $groupContents = $this->main->getGroupContents('default', $groupId);    // 환경설정에 있는 값으로 가져오도록 고쳐야 함.

        $skin = 'default';   // default를 가리킴, 환경설정에 있는 값으로 가져오도록 고쳐야 함.

        return view($skin, $groupContents);
    }
}
