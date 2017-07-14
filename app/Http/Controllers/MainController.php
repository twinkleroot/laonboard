<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main;
use Cache;
use App\Popup;

class MainController extends Controller
{
    public $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    // 홈페이지 메인 (메인은 레이아웃스킨 + 메인스킨 + 최근게시물스킨 조합)
    public function index(Request $request)
    {
        $skin = Cache::get('config.theme')->name ? : 'default';
        $mainContents = $this->main->getMainContents(Cache::get('config.skin')->latest, 'default');

        $popup = new Popup();
        $mainContents['popups'] = $popup->getPopupData();

        return view('layout.'. $skin. '.main', $mainContents);
    }

    // 게시판 그룹별 메인 (그룹별 메인은 레이아웃스킨 + 메인스킨 + 최근게시물스킨 조합)
    public function groupIndex($groupId)
    {
        $skin = Cache::get('config.theme')->name ? : 'default';
        $groupContents = $this->main->getGroupContents($groupId, Cache::get('config.skin')->latest, 'default');

        return view('layout.'. $skin. '.group', $groupContents);
    }
}
