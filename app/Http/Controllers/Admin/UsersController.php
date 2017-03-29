<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use App\Config;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('level:10');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('level', 'desc')->get();
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
        $config = Config::getConfig('config.join');
        return view('admin.user.create')
            ->with('user', $user)
            ->with('config', $config)
            ;
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

        $validator = Validator::make($request->all(), User::$rules);

        if ($validator->fails()) {
            return redirect(route('users.create'))
                        ->withErrors($validator)
                        ->withInput();
        }

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
            'addr1' => $request->get('addr1'),
            'addr2' => $request->get('addr2'),
            // 'addr3' => $request->get('addr3'),
            'zip' => $request->get('zip'),
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

        return redirect(route('users.index'))->with('message', $user->nick . ' 회원이 추가되었습니다.');
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
        $user = User::findOrFail($id);

        if($request->get('change_password') !== '') {
            $user->password = bcrypt($request->get('change_password'));

            $user->save();
        }

        $user->update([
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

        return redirect(route('users.index'))->with('message', $user->nick . '의 회원정보가 수정되었습니다.');
    }

    /**
    *  선택 수정 기능
    */
    public function selectedUpdate(Request $request)
    {
        $ids = $request->get('ids');
        $opens = $request->get('opens');
        $mailings = $request->get('mailings');
        $smss = $request->get('smss');
        $intercepts = $request->get('intercepts');
        $levels = $request->get('levels');

        $idArr = explode(',', $ids);
        $openArr = explode(',', $opens);
        $mailingArr = explode(',', $mailings);
        $smsArr = explode(',', $smss);
        $interceptArr = explode(',', $intercepts);
        $levelArr = explode(',', $levels);

        $index = 0;
        foreach($idArr as $id) {
            $user = User::find($id);

            if(!is_null($user)) {
                $user->update([
                    // 'certify' => $request->get('certify'),
                    'open' => $openArr[$index] == '1' ? 1 : 0,
                    'mailing' => $mailingArr[$index] == '1' ? 1 : 0,
                    'sms' => $smsArr[$index] == '1' ? 1 : 0,
                    // 'adult' => $request->get('adult') == '1' ? 1 : 0,
                    'intercept_date' => $interceptArr[$index] == 1 ? Carbon::now()->format('Ymj') : null ,
                    'level' => $levelArr[$index],
                ]);
                $index++;
            } else {
                abort('500', '정보를 수정할 회원이 존재하지 않습니다. 회원이 잘 선택 되었는지 확인해 주세요.');
            }
        }

        return redirect(route('users.index'))->with('message', '선택한 회원정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $ids = $request->get('ids');
        $deletedUser = User::whereRaw('id in (' . $ids . ') ')->delete();

        return redirect(route('users.index'))->with('message', '선택한 회원정보가 삭제되었습니다.');
    }
}
