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
            'redirect' => Session::has('redirect') ? Session::get('redirect') : ''
        ]);
    }
}
