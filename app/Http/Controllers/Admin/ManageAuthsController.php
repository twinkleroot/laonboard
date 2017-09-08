<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\ManageAuth;

class ManageAuthsController extends Controller
{
    public $authModel;

    public function __construct(ManageAuth $auth)
    {
        $this->middleware('super');

        $this->authModel = $auth;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = $this->authModel->getIndexParams($request);

        return view('admin.configs.manage_auth', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [ 'email' => 'bail|email|required'];
        $messages = [
            'email.email' => '받는 메일주소에 올바른 이메일 형식으로 입력해 주세요.',
            'email.required' => '받는 메일주소를 입력해 주세요.',
        ];
        $this->validate($request, $rules, $messages);

        $message = $this->authModel->storeManageAuth($request);

        return redirect()->back()->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ids)
    {
        $message = $this->authModel->deleteManageAuth($ids);

        return redirect()->back()->with('message', $message);
    }
}
