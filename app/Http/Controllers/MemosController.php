<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use Exception;

class MemosController extends Controller
{
    public $memo;

    public function __construct(Memo $memo)
    {
        $this->memo = $memo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $params = $this->memo->getIndexParams($request);

        return viewDefault("$theme.memos.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $params = [];
        try {
            $params = $this->memo->getCreateParams($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return viewDefault("$theme.memos.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'recv_nicks' => 'required',
            'memo' => 'required',
        ];
        $messages = [
            'recv_nicks.required' => '받는 회원 닉네임을 1개이상 입력해 주세요.',
            'memo.required' => '내용을 입력해 주세요.',
        ];

        $this->validate($request, $rules, $messages);

        try {
            $this->memo->storeMemo($request);
        } catch (Exception $e) {
            return alertRedirect($e->getMessage(), route('memo.index'). '?kind=send');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $params = [];
        try {
            $params = $this->memo->getShowParams($id, $request);
        } catch (Exception $e) {
            return alert($e->getMessage());
        }

        return viewDefault("$theme.memos.show", $params);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if($this->memo->deleteMemo($id)) {
            return redirect(route('memo.index'). '?kind='. $request->kind);
        } else {
            return alert('쪽지 삭제에 실패하였습니다.');
        }

    }
}
