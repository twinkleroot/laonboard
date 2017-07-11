<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin\Config;
use App\Admin\AdminUser;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use Cache;
use App\GroupUser;
use App\Common\Util;

class UsersController extends Controller
{
    public $userModel;

    public function __construct(AdminUser $userModel)
    {
        $this->userModel = $userModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->cant('index', $this->userModel)) {
            abort(403, '회원 목록 보기에 대한 권한이 없습니다.');
        }

        $params = $this->userModel->userList($request);

        return view('admin.users.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->cant('create', AdminUser::class)) {
            abort(403, '회원 추가에 대한 권한이 없습니다.');
        }

        $user = auth()->user();
        return view('admin.users.create', [
                'user' => $user,
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
        if (auth()->user()->cant('create', AdminUser::class)) {
            abort(403, '회원 추가에 대한 권한이 없습니다.');
        }

        $adminConfig = new Config();
        $rulePassword = $adminConfig->getPasswordRuleByConfigPolicy();
        $rule = [
            'email' => 'required|email|max:255|unique:users',
            'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
            'password' => $rulePassword[0] . '|' . $rulePassword[2],
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
        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

        $user;
        if(mb_strlen($id, 'utf-8') > 10) {  // 커뮤니티 쪽에서 들어올 때 user의 id가 아닌 id_hashKey가 넘어온다.
            $user = User::where('id_hashkey', $id)->first();
        } else {
            $user = User::find($id);
        }
        if( is_null($user) ) {
            return view('message', ['message' => '존재하지 않는 회원입니다.', 'redirect' => '/index' ]);
        }
        return view('admin.users.edit', [
                'title' => Cache::get("config.homepage")->title,
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
        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

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
        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

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
        if (auth()->user()->cant('delete', $this->userModel)) {
            abort(403, '회원 삭제에 대한 권한이 없습니다.');
        }

        $ids = $request->get('ids');
        $result = User::whereRaw('id in (' . $ids . ') ')->delete();

        return redirect(route('admin.users.index'))->with('message', '선택한 회원이 삭제되었습니다.');
    }
}
