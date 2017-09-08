<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BoardNew;
use Cache;

class BoardNewsController extends Controller
{
    public $boardNew;

    public function __construct(BoardNew $boardNew)
    {
        $this->boardNew = $boardNew;
    }

    public function index(Request $request)
    {
        // 기본환경설정에서 최근 게시물 설정일 보다 더 지난 글은 삭제한다.
        $this->boardNew->deleteOldWrites();

        $params = $this->boardNew->getIndexParams($request);
        $skin = Cache::get('config.homepage')->newSkin ? : 'default';

        return viewDefault("new.$skin.index", $params);
    }

    public function destroy(Request $request)
    {
        $this->boardNew->deleteWrites($request->chkId);

        return redirect(route('new.index'));
    }
}
