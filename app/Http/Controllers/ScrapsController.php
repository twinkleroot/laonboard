<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scrap;
use App\Models\Board;
use Exception;

class ScrapsController extends Controller
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

        $theme = cache('config.theme')->name;

        return viewDefault("$theme.scraps.index", $params);
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

        $theme = cache('config.theme')->name;

        return viewDefault("$theme.scraps.form", [
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
        event(new \App\Events\CreateComment($request));

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
