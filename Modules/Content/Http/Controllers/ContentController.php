<?php

namespace Modules\Content\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Content\Models\Content;

class ContentController extends Controller
{
    public $content;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (auth()->user()->cant('index', $this->content)) {
            abort(403, '내용관리 목록 보기에 대한 권한이 없습니다.');
        }

        $params['contents'] = $this->content->getContentList();

        return view("modules.content.admin.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', Content::class)) {
            abort(403, '내용관리 추가에 대한 권한이 없습니다.');
        }

        $params = $this->content->getContentCreate();

        return view("modules.content.admin.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', Content::class)) {
            abort(403, '내용관리 추가에 대한 권한이 없습니다.');
        }

        $rules = $this->rules();
        $rules = array_add($rules, 'content_id', 'bail|required|max:20|unique:contents|regex:/^[a-zA-Z0-9_]+$/');

        $this->validate($request, $rules, $this->messages());

        $result = $this->content->storeContent($request);

        return redirect(route('admin.content.edit', $result))
            ->withMessage($request->subject. '을(를) 생성하였습니다.');
    }

    /**
     * Show the specified resource.
     * @param  string  $contentId
     * @return Response
     */
    public function show($contentId)
    {
        $params = $this->content->getContentView($contentId);
        // Open Graph image 추가
        $params['ogImage'] = pullOutImage($params['content']->content);

        $skin = $params['content']->skin ? : 'default';

        return view("modules.content.skins.$skin.show", $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (auth()->user()->cant('update', $this->content)) {
            abort(403, '내용관리 수정에 대한 권한이 없습니다.');
        }

        $params = $this->content->getContentEdit($id);

        return view("modules.content.admin.form", $params);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('update', $this->content)) {
            abort(403, '내용관리 수정에 대한 권한이 없습니다.');
        }

        $this->validate($request, $this->rules(), $this->messages());

        $result = $this->content->updateContent($request, $id);

        return redirect(route('admin.content.edit', $result))
            ->withMessage('수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->content)) {
            abort(403, '내용관리 삭제에 대한 권한이 없습니다.');
        }

        $this->content->deleteContent($id);

        return redirect()->back()
            ->withMessage('삭제되었습니다.');
    }

    // 유효성 검사 규칙
    public function rules()
    {
        return [
            'subject' => 'bail|required',
            'content' => 'bail|required',
            'skin' => 'bail|required'
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'content_id.required' => 'ID를 입력해 주세요.',
            'content_id.max' => 'ID는 20자리를 넘길 수 없습니다.',
            'content_id.unique' => '이미 등록된 ID입니다. 다른 ID를 입력해 주세요.',
            'content_id.regex' => 'ID는 20자 이내의 영문자, 숫자, _ 만 가능합니다.',
            'subject.required'  => '제목을 입력해 주세요.',
            'content.required'  => '내용을 입력해 주세요.',
            'skin.required'  => '스킨을 선택해 주세요.',
        ];
    }
}
