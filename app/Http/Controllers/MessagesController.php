<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class MessagesController extends Controller
{
    public function message(Request $request)
    {
        return view('common.message', [
            'message' => Session::has('message') ? Session::get('message') : '',
            'redirect' => Session::has('redirect') ? Session::get('redirect') : '',
            'popup' => Session::has('popup') ? Session::get('popup') : '',
            'openerRedirect' => Session::has('openerRedirect') ? Session::get('openerRedirect') : '',
        ]);
    }

    public function confirm(Request $request)
    {
        return view('common.confirm', [
            'message' => Session::has('message') ? Session::get('message') : '',
            'redirect' => Session::has('redirect') ? Session::get('redirect') : '',
        ]);
    }
}
