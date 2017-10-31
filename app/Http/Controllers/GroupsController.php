<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupsController extends Controller
{
    public $groupModel;

    public function __construct(Group $groupModel)
    {
        $this->groupModel = $groupModel;
    }

    // 게시판 그룹별 메인 (그룹별 메인은 레이아웃스킨 + 메인스킨 + 최근게시물스킨 조합)
    public function index($groupId)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $params = $this->groupModel->getGroupContents($groupId, cache('config.skin')->latest, 'default');

        return viewDefault("$theme.groups.index", $params);
    }
}
