<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        return view('admin.user.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();
        return view('admin.user.create')->with('user', $user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $password = bcrypt($request->get('password'));

        $user = new User([
            'email' => $request->get('email'),
            'password' => $password,
            'name' => $request->get('name'),
            'nick' => $request->get('nick'),
            'level' => $request->get('level'),
            'point' => $request->get('point'),
            'homepage' => $request->get('homepage'),
            'hp' => $request->get('hp'),
            'tel' => $request->get('tel'),
            'certify' => $request->get('certify'),
            'adult' => $request->get('adult'),
            // 'addr1' => $request->get('addr1'),
            // 'addr2' => $request->get('addr2'),
            // 'addr3' => $request->get('addr3'),
            // 'zip' => $request->get('zip'),
            'mailing' => $request->get('mailing'),
            'sms' => $request->get('sms'),
            'open' => $request->get('open'),
            'signature' => $request->get('signature'),
            'profile' => $request->get('profile'),
            'memo' => $request->get('memo'),
            'leave_date' => $request->get('leave_date'),
            'intercept_date' => $request->get('intercept_date'),
            // 본인확인방법, 회원아이콘은 다른데로 추가되는 듯.
        ]);

        $user->save();

        return redirect('/users')->with('message', $user->nick . ' 회원이 추가되었습니다.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.user.edit')->with('user', $user)->with('id', $id);
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
        $user = User::find($id);
        $password = $user->password;
        if($request->get('password') !== '') {
            $password = bcrypt($request->get('password'));
        }

        $user->update([
            'password' => $password,
            'name' => $request->get('name'),
            'nick' => $request->get('nick'),
            'level' => $request->get('level'),
            'point' => $request->get('point'),
            'homepage' => $request->get('homepage'),
            'hp' => $request->get('hp'),
            'tel' => $request->get('tel'),
            'certify' => $request->get('certify'),
            'adult' => $request->get('adult'),
            // 'addr1' => $request->get('addr1'),
            // 'addr2' => $request->get('addr2'),
            // 'addr3' => $request->get('addr3'),
            // 'zip' => $request->get('zip'),
            'mailing' => $request->get('mailing'),
            'sms' => $request->get('sms'),
            'open' => $request->get('open'),
            'signature' => $request->get('signature'),
            'profile' => $request->get('profile'),
            'memo' => $request->get('memo'),
            'leave_date' => $request->get('leave_date'),
            'intercept_date' => $request->get('intercept_date'),
            // 본인확인방법, 회원아이콘은 다른데서 변경하는 듯.
        ]);

        return redirect('/users')->with('message', $user->nick . '의 회원정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ids)
    {
        //
    }
}
