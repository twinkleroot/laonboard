<?php

namespace App\Http\Controllers\Board;

use App\Scrap;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        return view('board.default.scrap_index', $params);
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
             return view('board.default.scrap_confirm', [
                 'confirm' => '이미 스크랩하신 글 입니다.\\n\\n지금 스크랩을 확인하시겠습니까?'
             ]);
        }

        $write = $this->scrap->getWrite($request);
        if($write->is_comment) {
            return view('message', [
                'message' => '코멘트는 스크랩 할 수 없습니다.',
                'popup' => 1
            ]);
        }

        return view('board.default.scrap_form', ['write' => $write, 'boardId' => $request->boardId]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $write = $this->scrap->getWrite($request);
        if( is_null($write)) {
            return view('message', [
                'message' => '스크랩하시려는 게시글이 존재하지 않습니다.',
                'popup' => 1
            ]);
        }

        $existScrap = $this->scrap->getScrap($request);
        if($existScrap) {
             return view('board.default.scrap_form', [
                 'confirm' => '이미 스크랩하신 글 입니다.\\n\\n지금 스크랩을 확인하시겠습니까?'
             ]);
        }

        $result = $this->scrap->storeScrap($request);

        if(isset($result['message'])) {
            return view('message', [
                'message' => $result['message'],
                'popup' => 1
            ]);
        } else {
            return view('board.default.scrap_form', [
                 'confirm' => '이 글을 스크랩 하였습니다.\\n\\n지금 스크랩을 확인하시겠습니까?'
            ]);
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
        $this->scrap->where([
            'user_id' => auth()->user()->id,
            'id' => $id
        ])->delete();

        return redirect()->back();
    }
}
