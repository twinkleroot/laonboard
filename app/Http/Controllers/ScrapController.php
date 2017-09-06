<?php

namespace App\Http\Controllers;

use App\Scrap;
use App\Board;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class ScrapController extends Controller
{
    public $scrap;

    public function __construct(Scrap $scrap)
    {
        $this->scrap = $scrap;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = $this->scrap->getIndexParams();

        return view('board.scrap_index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $existScrap = $this->scrap->getScrap($request);
        if($existScrap) {
            return confirm('이미 스크랩하신 글 입니다.\\n\\n지금 스크랩을 확인하시겠습니까?', route('scrap.index'));
        }

        $write = $this->scrap->getWrite($request);
        if($write->is_comment) {
            return alertClose('코멘트는 스크랩 할 수 없습니다.');
        }

        return view('board.scrap_form', [
            'write' => $write,
            'boardId' => Board::getBoard($request->boardName, 'table_name')->id,
            'boardName' => $request->boardName
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = '';
        try {
            $result = $this->scrap->storeScrap($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        if($result == 'exist'){
            return confirm('이미 스크랩하신 글 입니다.\\n\\n지금 스크랩을 확인하시겠습니까?', route('scrap.index'));
        } else {
            return confirm('이 글을 스크랩 하였습니다.\\n\\n지금 스크랩을 확인하시겠습니까?', route('scrap.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Scrap  $scrap
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = auth()->user() ? auth()->user()->id : 0;

        $this->scrap->where([
            'user_id' => $userId,
            'id' => $id
        ])->delete();

        return redirect()->back();
    }
}
