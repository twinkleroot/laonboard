<?php

namespace App\Http\Controllers\BoardNew;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BoardNew;
use Cache;

class BoardNewController extends Controller
{
    public $boardNew;

    public function __construct(BoardNew $boardNew)
    {
        $this->boardNew = $boardNew;
    }

    public function index(Request $request)
    {
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
