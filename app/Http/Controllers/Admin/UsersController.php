<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config;
use App\Models\AdminUser;
use App\Models\User as AppUser;

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

        return view("admin.users.index", $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', AdminUser::class)) {
            abort(403, '회원 추가에 대한 권한이 없습니다.');
        }

        $params = [
            'user' => auth()->user(),
            'type' => 'create',
        ];

        return view("admin.users.form", $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('create', AdminUser::class)) {
            abort(403, '회원 추가에 대한 권한이 없습니다.');
        }

        // icon 확장자 확인을 위해 이미지 파일의 원래 이름을 가져온다.
        if($request->icon) {
            $request->merge([
                'iconName' => $request->icon->getClientOriginalName()
            ]);
        }

        $userModel = new AppUser();
        $messages = $this->messages();
        $messages = $userModel->addPasswordMessages($messages);

        $this->validate($request, $this->rules(), $messages);

        $id = $this->userModel->storeUser($request);

        return redirect(route('admin.users.edit', $id));
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

        $user = AdminUser::find($id);
        if(!$user) {
            $user = AdminUser::whereIdHashkey($id)->first();
            if(!$user) {
                return alertRedirect('존재하지 않는 회원입니다.', '/admin/index');
            }
        }

        $params = $this->userModel->editParams($user, $id);

        return view("admin.users.form", $params);
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
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('update', $this->userModel)) {
            abort(403, '회원 정보 수정에 대한 권한이 없습니다.');
        }

        $beforeUserInfo = AppUser::getUser($id);
        $rules = $this->rules();

        $rules = array_except($rules, 'email');
        if(!$request->change_password) {
            $rules = array_except($rules, 'password');
        } else {
            $request->merge([
                'password' => $request->change_password
            ]);
        }
        if($beforeUserInfo->name == $request->name) {
            $rules = array_except($rules, 'name');
        }
        if($beforeUserInfo->nick == $request->nick) {
            $rules = array_except($rules, 'nick');
        }

        // icon 확장자 확인을 위해 이미지 파일의 원래 이름을 가져온다.
        if($request->icon) {
            $request->merge([
                'iconName' => $request->icon->getClientOriginalName()
            ]);
        }

        $userModel = new AppUser();
        $messages = $this->messages();
        $messages = $userModel->addPasswordMessages($messages);

        $this->validate($request, $rules, $messages);

        $this->userModel->updateUserInfo($request, $id);

        return redirect()->back()->with('message', '회원정보가 수정되었습니다.');
    }

    /**
    *  선택 수정 기능
    */
    public function selectedUpdate(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

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
    public function destroy(Request $request)
    {
        if(isDemo()) {
            return alert('데모 화면에서는 하실(보실) 수 없는 작업입니다.');
        }

        if (auth()->user()->cant('delete', $this->userModel)) {
            abort(403, '회원 삭제에 대한 권한이 없습니다.');
        }

        $this->userModel->deleteUser($request);

        return redirect(route('admin.users.index'))->with('message', '선택한 회원이 삭제되었습니다.');
    }

    // 유효성 검사 규칙
    public function rules()
    {
        $adminConfig = new Config();
        $rulePassword = $adminConfig->getPasswordRuleByConfigPolicy();

        return [
            'email' => 'bail|required|email|max:255|unique:users',
            'password' => 'bail|'. $rulePassword[0] . '|' . $rulePassword[2],
            'name' => 'bail|alpha_dash|nullable',
            'nick' => 'bail|required|nick_length:2,4|unique:users',
            'level' => 'bail|required|numeric',
            'point' => 'bail|numeric|nullable',
            'leave_date' => 'bail|date_format:"Ymd"|nullable',
            'intercept_date' => 'bail|date_format:"Ymd"|nullable',
            'homepage' => 'bail|regex:'. config('laon.URL_REGEX'). '/|nullable',
            'tel' => 'bail|regex:/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/|nullable',
            'hp' => 'bail|regex:/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/|nullable',
            'addr1' => 'nullable',
            'addr2' => 'nullable',
            'signature' => 'nullable',
            'profile' => 'nullable',
            'memo' => 'nullable',
            'recommend' => 'bail|nick_length:2,4|nullable',
            'iconName' => 'bail|regex:/\.(gif)$/i|nullable'
        ];
    }

    // 에러 메세지
    public function messages()
    {
        return [
            'email.required' => '이메일을 입력해 주세요.',
            'email.email' => '이메일에 올바른 Email양식으로 입력해 주세요.',
            'email.max' => '이메일은 :max자리를 넘길 수 없습니다.',
            'email.unique' => '이미 등록된 이메일입니다. 다른 이메일을 입력해 주세요.',
            'password.required' => '비밀번호를 입력해 주세요.',
            'name.alpha_dash' => '이름에 영문자, 한글, 숫자, 대쉬(-), 언더스코어(_)만 입력해 주세요.',
            'nick.required' => '닉네임을 입력해 주세요.',
            'nick.nick_length' => '닉네임의 길이는 한글 :half자, 영문 :min자 이상이어야 합니다.',
            'nick.unique' => '이미 등록된 닉네임입니다. 다른 닉네임을 입력해 주세요.',
            'level.required' => '회원권한을 선택해 주세요.',
            'level.numeric' => '회원권한에는 숫자만 들어갈 수 있습니다.',
            'point.numeric' => '포인트에는 숫자만 들어갈 수 있습니다.',
            'leave_date.date_format' => '탈퇴일자에 올바른 날짜 형식(Ymd)으로 입력해 주세요.',
            'intercept_date.date_format' => '접근차단일자에 올바른 날짜 형식(Ymd)으로 입력해 주세요.',
            'homepage.regex' => '홈페이지에 올바른 url 형식으로 입력해 주세요.',
            'tel.regex' => '전화번호에 전화번호형식(000-0000-0000)으로 입력해 주세요.',
            'hp.regex' => '휴대폰번호에 전화번호형식(000-0000-0000)으로 입력해 주세요.',
            'recommend.nick_length' => '추천인의 길이는 한글 :half자, 영문 :min자 이상이어야 합니다.',
            'iconName.regex' => '회원아이콘에는 확장자가 gif인 이미지 파일만 들어갈 수 있습니다.',
        ];
    }
}
