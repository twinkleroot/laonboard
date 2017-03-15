<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class UserController extends Controller
{
    public function edit() {
        $user = Auth::user();
        return view('user.edit')->with('user', $user);
    }

    public function update(Request $request) {
        return redirect('/home')->with('message', $request->get('nick') . '님의 회원정보가 변경되었습니다.');
    }
}
