<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use App\Config;
use App\GroupUser;

class UsersController extends Controller
{
    public $config;
    public $userModel;
    public $rulePassword;

    public function __construct(Config $config, User $userModel)
    {
        $this->middleware('level:10');

        $this->config = Config::getConfig('config.join');
        $this->rulePassword = Config::getRulePassword('config.join', $this->config);
        $this->userModel = $userModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userModel->userList();

        return view('admin.users.index', [
            'title' => Config::getConfig('config.homepage')->title,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();
        return view('admin.users.create', [
                'title' => Config::getConfig('config.homepage')->title,
                'user' => $user,
                'config' => $this->config
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = [
            'email' => 'required|email|max:255|unique:users',
            'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
            'password' => $this->rulePassword[0] . '|' . $this->rulePassword[2],
        ];

        $this->validate($request, $rule);

        $user = $this->userModel->addUser($request->all());
        if(is_null($user)) {
            abort('500', '회원추가가 실패하였습니다.');
        }
        return redirect(route('admin.users.index'))->with('message', $user->nick . ' 회원이 추가되었습니다.');
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
        return view('admin.users.edit', [
                'title' => Config::getConfig('config.homepage')->title,
                'user' => $user,
                'id' => $id
            ]);
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
            'addr1' => $request->get('addr1'),
            'addr2' => $request->get('addr2'),
            'zip' => $request->get('zip'),
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

        return redirect(route('admin.users.index'))->with('message', $user->nick . '의 회원정보가 수정되었습니다.');
    }

    /**
    *  선택 수정 기능
    */
    public function selectedUpdate(Request $request)
    {
        $this->userModel->selectedUpdate($request);

        return redirect(route('admin.users.index'))->with('message', '선택한 회원정보가 수정되었습니다.');
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
        $result = User::whereRaw('id in (' . $ids . ') ')->delete();

        return redirect(route('admin.users.index'))->with('message', '선택한 회원정보가 삭제되었습니다.');
    }
}
