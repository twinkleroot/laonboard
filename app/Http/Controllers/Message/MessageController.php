<?php

namespace App\Http\Controllers\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class MessageController extends Controller
{
    public function message(Request $request)
    {
        $message = '';
        // dump($request);
        if(Session::has('message')) {
            $message = Session::get('message');
        }

        return view('message')->with('message', $message);
    }
}
