<?php

namespace App\Http\Controllers\Content;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Content;

class ContentController extends Controller
{
    public $content;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contents = $this->content->getContentList();

        return view('content.index', [
            'contents' => $contents
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = $this->content->getContentView($id);
        $skin = $params['content']->skin ? : 'default';

        return view('content.'. $skin. '.show', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->content->getContentCreate();

        return view('content.form', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $this->rules();
        $rules = array_add($rules, 'content_id', 'bail|required|max:20|unique:contents|regex:/^[a-zA-Z0-9_]+$/');

        $this->validate($request, $this->rules(), $this->messages());

        $result = $this->content->storeContent($request);

        return redirect(route('contents.edit', $result));
    }

    public function rules()
    {
        return [
            'subject' => 'bail|required',
            'content' => 'bail|required',
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
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $params = $this->content->getContentEdit($id);

        return view('content.form', $params);
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
        $this->validate($request, $this->rules(), $this->messages());

        $result = $this->content->updateContent($request, $id);

        if(!$result) {
            return view('message', [
                'message' => '내용변경에 실패하였습니다.'
            ]);
        }

        return redirect(route('contents.edit', $result));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->content->deleteContent($id);

        return redirect()->back();
    }
}
