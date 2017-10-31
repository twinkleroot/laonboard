<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Point;
use Exception;

class PointsController extends Controller
{

    public $pointModel;

    public function __construct(Point $point)
    {
        $this->pointModel = $point;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->pointModel)) {
            abort(403, '포인트 관리 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->pointModel->getPointIndexParams($request);

        return view("admin.points.index", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->cant('create', $this->pointModel)) {
            abort(403, '포인트 추가에 대한 권한이 없습니다.');
        }

        $rules = [
            'email' => 'bail|required|email',
            'content' => 'bail|required',
            'point' => 'bail|required|numeric',
        ];

        $messages = [
            'email.required' => '회원 이메일을 입력해 주세요.',
            'email.email' => '회원 이메일에 올바른 Email양식으로 입력해 주세요.',
            'content.required' => '포인트내용을 입력해 주세요.',
            'point.required' => '포인트를 입력해 주세요.',
            'point.numeric' => '포인트에는 숫자만 들어갈 수 있습니다.',
        ];

        $this->validate($request, $rules, $messages);

        $this->pointModel->givePoint($request);

        return redirect()->back()->withMessage($request->email. '회원의 포인트 증감 설정이 완료되었습니다. 포인트 내용 : '. $request->content. ', 부여한 포인트 : '. $request->point);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->pointModel)) {
            abort(403, '포인트 삭제에 대한 권한이 없습니다.');
        }

        $this->pointModel->deletePointOnAdmin($id);

        return redirect()->back();
    }
}
