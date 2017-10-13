<?php

namespace Modules\Memo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use App\Http\Controllers\Controller;
use App\Memo;
use App\Services\ReCaptcha;
use Exception;

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

        return viewDefault("memo.$skin.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $skin = cache('config.skin')->memo ? : 'default';
        $params = [];
        try {
            $params = $this->memo->getCreateParams($request);
        } catch (Exception $e) {
            return alertClose($e->getMessage());
        }

        return viewDefault("memo.$skin.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!auth()->check() || !auth()->user()->isSuperAdmin()) {
            ReCaptcha::reCaptcha($request);	// 구글 리캡챠 체크
        }
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
        $skin = cache('config.skin')->memo ? : 'default';
        $params = [];
        try {
            $params = $this->memo->getShowParams($id, $request);
        } catch (Exception $e) {
            return alert($e->getMessage());
        }

        return viewDefault("memo.$skin.show", $params);
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

//
// class MemoController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      * @return Response
//      */
//     public function index()
//     {
//         return view('memo::index');
//     }
//
//     /**
//      * Show the form for creating a new resource.
//      * @return Response
//      */
//     public function create()
//     {
//         return view('memo::create');
//     }
//
//     /**
//      * Store a newly created resource in storage.
//      * @param  Request $request
//      * @return Response
//      */
//     public function store(Request $request)
//     {
//     }
//
//     /**
//      * Show the specified resource.
//      * @return Response
//      */
//     public function show()
//     {
//         return view('memo::show');
//     }
//
//     /**
//      * Show the form for editing the specified resource.
//      * @return Response
//      */
//     public function edit()
//     {
//         return view('memo::edit');
//     }
//
//     /**
//      * Update the specified resource in storage.
//      * @param  Request $request
//      * @return Response
//      */
//     public function update(Request $request)
//     {
//     }
//
//     /**
//      * Remove the specified resource from storage.
//      * @return Response
//      */
//     public function destroy()
//     {
//     }
// }
