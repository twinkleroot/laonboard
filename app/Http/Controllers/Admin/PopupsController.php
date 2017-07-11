<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Popup;

class PopupsController extends Controller
{
    public $popup;

    public function __construct(Popup $popup)
    {
        $this->popup = $popup;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->cant('index', $this->popup)) {
            abort(403, '팝업 레이어 관리 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->popup->getIndexParams();

        return view('admin.popups.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(auth()->user()->cant('create', Popup::class)) {
            abort(403, '팝업 레이어 생성에 대한 권한이 없습니다.');
        }

        $params = $this->popup->getCreateParams();

        return view('admin.popups.form', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->cant('create', Popup::class)) {
            abort(403, '팝업 레이어 생성에 대한 권한이 없습니다.');
        }

        $result = $this->popup->storePopup($request);

        return redirect(route('admin.popups.edit', $result->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(auth()->user()->cant('update', $this->popup)) {
            abort(403, '팝업 레이어 수정에 대한 권한이 없습니다.');
        }

        $params = $this->popup->getEditParams($id);

        return view('admin.popups.form', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(auth()->user()->cant('update', $this->popup)) {
            abort(403, '팝업 레이어 수정에 대한 권한이 없습니다.');
        }

        $result = $this->popup->updatePopup($request, $id);

        return redirect(route('admin.popups.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(auth()->user()->cant('delete', $this->popup)) {
            abort(403, '팝업 레이어 삭제에 대한 권한이 없습니다.');
        }

        $result = $this->popup->deletePopup($id);

        return redirect()->back();
    }
}
