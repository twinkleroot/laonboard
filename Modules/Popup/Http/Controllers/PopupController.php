<?php

namespace Modules\Popup\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Popup\Models\Popup;

class PopupController extends Controller
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

        return view('modules.popup.admin.index', $params);
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

        return view('modules.popup.admin.form', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        if(auth()->user()->cant('create', Popup::class)) {
            abort(403, '팝업 레이어 생성에 대한 권한이 없습니다.');
        }

        $id = $this->popup->storePopup($request);

        return redirect(route('admin.popup.edit', $id));
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

        return view('modules.popup.admin.form', $params);
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
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        if(auth()->user()->cant('update', $this->popup)) {
            abort(403, '팝업 레이어 수정에 대한 권한이 없습니다.');
        }

        $result = $this->popup->updatePopup($request, $id);

        return redirect(route('admin.popup.edit', $id));
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

        if(auth()->user()->cant('delete', $this->popup)) {
            abort(403, '팝업 레이어 삭제에 대한 권한이 없습니다.');
        }

        $result = $this->popup->deletePopup($id);

        return redirect()->back()->withMessage('삭제하였습니다.');
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'disable_hours' => 'bail|numeric|required',
            'begin_time' => 'bail|date_format:"Y-m-d H:i:s"|required',
            'end_time' => 'bail|date_format:"Y-m-d H:i:s"|required',
            'left' => 'bail|numeric|required',
            'top' => 'bail|numeric|required',
            'width' => 'bail|numeric|required',
            'height' => 'bail|numeric|required',
            'color' => 'bail|required',
            'color_button' => 'bail|required',
            'color_button_font' => 'bail|required',
            'subject' => 'bail|required',
            'content' => 'bail|required',
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'disable_hours.numeric' => '시간에는 숫자만 들어갈 수 있습니다.',
            'disable_hours.required' => '시간을 입력해 주세요.',
            'begin_time.date_format' => '시작일시에 올바른 날짜 형식(Y-m-d H:i:s)으로 입력해 주세요.',
            'begin_time.required' => '시작일시를 입력해 주세요.',
            'end_time.date_format' => '종료일시에 올바른 날짜 형식(Y-m-d H:i:s)으로 입력해 주세요.',
            'end_time.required' => '종료일시를 입력해 주세요.',
            'left.numeric' => '팝업레이어 좌측 위치에는 숫자만 들어갈 수 있습니다.',
            'left.required' => '팝업레이어 좌측 위치를 입력해 주세요.',
            'top.numeric' => '팝업레이어 상단 위치에는 숫자만 들어갈 수 있습니다.',
            'top.required' => '팝업레이어 상단 위치를 입력해 주세요.',
            'width.numeric' => '팝업레이어 넓이에는 숫자만 들어갈 수 있습니다.',
            'width.required' => '팝업레이어 넓이를 입력해 주세요.',
            'height.numeric' => '팝업레이어 높이에는 숫자만 들어갈 수 있습니다.',
            'height.required' => '팝업레이어 높이를 입력해 주세요.',
            'color.required' => '팝업레이어 색상을 입력해 주세요.',
            'color_button.required' => '팝업레이어 버튼 색상을 입력해 주세요.',
            'color_button_font.required' => '팝업레이어 버튼 폰트 색상을 입력해 주세요.',
            'subject.required' => '팝업 제목을 입력해 주세요.',
            'content.required' => '내용을 입력해 주세요.',
        ];
    }
}
