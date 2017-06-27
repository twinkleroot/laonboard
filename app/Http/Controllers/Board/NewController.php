<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BoardNew;
use Cache;

class NewController extends Controller
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

        return view('board.'. $skin. '.new', $params);
    }

    public function destroy(Request $request)
    {
        $message = $this->boardNew->deleteWrites($request->chkId);

        if($message != '') {
            return view('message', [
                'message' => $message,
            ]);
        }

        return redirect(route('new.index'));
    }
}
