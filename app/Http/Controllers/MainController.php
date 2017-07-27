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
        $skin = cache('config.theme')->name ? : 'default';
        $params = $this->main->getMainContents(cache('config.skin')->latest, 'default');

        $popup = new Popup();
        $params['popups'] = $popup->getPopupData();

        return viewDefault("layout.$skin.main", $params);
    }

    // 게시판 그룹별 메인 (그룹별 메인은 레이아웃스킨 + 메인스킨 + 최근게시물스킨 조합)
    public function groupIndex($groupId)
    {
        $skin = cache('config.theme')->name ? : 'default';
        $params = $this->main->getGroupContents($groupId, cache('config.skin')->latest, 'default');

        return viewDefault("layout.$skin.group", $params);
    }
}
