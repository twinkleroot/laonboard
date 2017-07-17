<?php

namespace App\Http\Controllers\Memo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Memo;
use App\ReCaptcha;

class MemoController extends Controller
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
        $skin = cache('config.skin')->memo ? : 'default';
        $params = $this->memo->getIndexParams($request);

        return view()->exists("memo.$skin.index") ? view("memo.$skin.index", $params) : view("memo.default.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $skin = cache('config.skin')->memo ? : 'default';
        $result = $this->memo->getCreateParams($request);

        if( isset($result['message']) ) {
            return view('message', $result);
        } else {
            if(isset($request->to)) {
                return view()->exists("memo.$skin.form") ? view("memo.$skin.form", $result) : view("memo.default.form", $result);
            } else {
                return view()->exists("memo.$skin.form") ? view("memo.$skin.form") : view("memo.default.form");
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(ReCaptcha::reCaptcha($request)) {    // 구글 리캡챠 체크
            $message = $this->memo->storeMemo($request);
            return view('message', [
                    'message' => $message,
                    'redirect' => route('memo.index'). '?kind=send'
            ]);
        } else {
            return redirect()->back()->withInput()->withErrors(['reCaptcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
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
        $skin = cache('config.skin')->memo ? : 'default';
        $params = $this->memo->getShowParams($id, $request);

        if( isset($params['message']) ) {
            return view('message', $params);
        }

        return view()->exists("memo.$skin.show") ? view("memo.$skin.show", $params) : view("memo.default.show", $params);
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
            return view('message', ['message' => '쪽지 삭제에 실패하였습니다.']);
        }

    }
}
