<?php

namespace App\Http\Controllers\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class MessageController extends Controller
{
    public function message(Request $request)
    {
        return view('message', [
            'message' => Session::has('message') ? Session::get('message') : '',
            'redirect' => Session::has('redirect') ? Session::get('redirect') : '',
            'popup' => Session::has('popup') ? Session::get('popup') : '',
        ]);
    }

    public function confirm(Request $request)
    {
        return view('confirm', [
            'message' => Session::has('message') ? Session::get('message') : '',
            'redirect' => Session::has('redirect') ? Session::get('redirect') : '',
        ]);
    }
}
