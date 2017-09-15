<?php

namespace App\Http\Controllers\Admin;

use Mail;
use Gate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\EmailSendTest;
use App\User;

class MailsController extends Controller
{
    public $menuCode = ['100500', 'r'];

    public function index()
    {
        if(auth()->user()->isSuperAdmin() || Gate::allows('view-admin-mailtest', getManageAuthModel($this->menuCode))) {
            return view('admin.configs.mail_test');
        } else {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }
    }

    public function postMail(Request $request)
    {
        if(auth()->user()->isSuperAdmin() || Gate::allows('view-admin-mailtest', getManageAuthModel($this->menuCode))) {
            $rules = [ 'email' => 'bail|email|required'];
            $messages = [
                'email.email' => '받는 메일주소에 올바른 이메일 형식으로 입력해 주세요.',
                'email.required' => '받는 메일주소를 입력해 주세요.',
            ];
            $this->validate($request, $rules, $messages);

            $toAddresses = explode(',', $request->email);
            $successAddress = [];
            foreach($toAddresses as $to) {
                $to = trim($to);
                Mail::to($to)->send(new EmailSendTest());
                array_push($successAddress, $to);
            }

            return redirect(route('admin.email'))->with('successAddress', $successAddress);
        } else {
            return alertRedirect('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', '/admin/index');
        }
    }
}
