<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Mail;
use File;
use Exception;
use App\Mail\FormMailSend;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $dates = ['today_login', 'email_certify', 'nick_date', 'open_date', ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $rulesRegister = [
        'email' => 'required|email|max:255|unique:users',
        'nick' => 'required|nick_length:2,4|unique:users',
        'password_confirmation' => 'required',
    ];

    public $rulesPassword = [
        'password_confirmation' => 'required',
    ];

    public $messages = [
        'email.required' => '이메일을 입력해 주세요.',
        'email.email' => '이메일에 올바른 Email양식으로 입력해 주세요.',
        'email.max' => '이메일은 :max자리를 넘길 수 없습니다.',
        'email.unique' => '이미 등록된 이메일입니다. 다른 이메일을 입력해 주세요.',
        'password.required' => '비밀번호를 입력해 주세요.',
        'password.confirmed' => '비밀번호 확인에 동일하게 입력해 주세요.',
        'password.regex' => '',
        'password_confirmation.required' => '비밀번호 확인을 입력해 주세요.',
        'name.alpha_dash' => '이름에 영문자, 한글, 숫자, 대쉬(-), 언더스코어(_)만 입력해 주세요.',
        'nick.required' => '닉네임을 입력해 주세요.',
        'nick.nick_length' => '닉네임의 길이는 한글 :half자, 영문 :min자 이상이어야 합니다.',
        'nick.unique' => '이미 등록된 닉네임입니다. 다른 닉네임을 입력해 주세요.',
        'homepage.regex' => '홈페이지에 올바른 url 형식으로 입력해 주세요.',
        'tel.regex' => '전화번호에 전화번호형식(000-0000-0000)으로 입력해 주세요.',
        'hp.regex' => '휴대폰번호에 전화번호형식(000-0000-0000)으로 입력해 주세요.',
        'recommend.nick_length' => '추천인의 길이는 한글 :half자, 영문 :min자 이상이어야 합니다.',
        'iconName.regex' => '회원아이콘에는 확장자가 gif인 이미지 파일만 들어갈 수 있습니다.',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function __construct()
    {
        $this->table = 'users';
    }

    // SocialLogin 모델과의 관계 설정
    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    // BoardGroup 모델과의 관계 설정
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')->withPivot('id', 'created_at');
    }

    // Point 모델과의 관계설정
    public function points()
    {
        return $this->hasMany(Point::class);
    }

    // Write 모델과의 관계설정
    public function writes()
    {
        return $this->hasMany(Write::class);
    }

    public function isAdmin()
    {
        if($this->isSuperAdmin()) {
            return true;
        }

        if(session()->get('admin')) {
            return true;
        } else {
            if(ManageAuth::where('user_id', auth()->user()->id)->where('isModule', 0)->first()) {
                session()->put('admin', true);
                return true;
            }
        }

        return false;
    }

    public function isSuperAdmin()
    {
        if($this->email === cache('config.homepage')->superAdmin) {
            return true;
        }
        return false;
    }

    public function isGroupAdmin($group)
    {
        if($this->isSuperAdmin()) {
            return true;
        }
        if($this->email === $group->admin) {
            return true;
        }
        return false;
    }

    public function isBoardAdmin($board)
    {
        if($this->isSuperAdmin()) {
            return true;
        }
        if($this->isGroupAdmin($board->group)) {
            return true;
        }
        if($this->email === $board->admin) {
            return true;
        }
        return false;
    }

    public static function getUser($id)
    {
        static $user;
        if (is_null($user) || $user->id != $id) {
            $user = User::find($id);
        }

        return $user;
    }

    // 회원 정보 수정 페이지에 전달할 데이터
    public function editParams()
    {
        $user = Auth::user();

        // 정보공개 변경여부
        $openChangable = $this->openChangable($user, Carbon::now());

        $socialLogins = SocialLogin::where('user_id', $user->id)->get();
        $socials = [
            'naver' => '',
            'kakao' => '',
            'facebook' => '',
            'google' => '',
        ];

        foreach($socialLogins as $sociallogin) {
            $socials[$sociallogin['provider']] = $sociallogin['social_id'];
        }

        $folder = getIconFolderName($user->created_at);
        $iconName = getIconName($user->id, $user->created_at);
        $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
        $url = '/storage/user/'. $folder. '/'. $iconName. '.gif';

        $editFormData = [
            'user' => $user,
            'config' => cache("config.join"),
            'openDate' => cache("config.homepage")->openDate,				// 정보공개 변경 가능일
            'nickChangable' => $this->nickChangable($user, Carbon::now()),  // 닉네임 변경여부
            'openChangable' => $openChangable[0],                           // 정보공개 변경 여부
            'dueDate' => $openChangable[1],                                 // 정보공개 언제까지 변경 못하는지 날짜
            'recommend' => $this->recommendedPerson($user),                 // 추천인 닉네임 id로 가져오기
            'socials' => $socials,                                          // 소셜에 연결한 정보
            'iconPath' => $path,
            'iconUrl' => $url,
        ];

        return $editFormData;
    }

    // 추천인 닉네임 구하기
    public function recommendedPerson($user)
    {
        $recommendedNick = '';
        if($user->recommend) {
            $recommendedUser = User::find($user->recommend);
            if($recommendedUser) {
                $recommendedNick = $recommendedUser->nick;
            }
        }

        return $recommendedNick;
    }

    // 닉네임 변경 가능 여부
    public function nickChangable($user, $current)
    {
        // 현재 시간과 로그인한 유저의 닉네임변경시간과의 차이
        $nickDiff = $current->diffInDays($user->nick_date);
        // 닉네임 변경 여부
        $nickChangable = false;
        if($nickDiff > cache("config.join")->nickDate) {
            $nickChangable = true;
        }

        return $nickChangable;
    }

    // 정보공개 변경 가능 여부
    public function openChangable($user, $current)
    {
        $configOpenDate = cache("config.homepage")->openDate;
        $openChangable = array(false, $current);

        $openDate = $user->open_date;

        if(is_null($openDate)) {
            $openChangable[0] = true;
        } else {
            $openDiff = $current->diffInDays($openDate);
            if($openDiff >= $configOpenDate) {
                $openChangable[0] = true;
            }
            $openChangable[1] = $openDate->addDays($configOpenDate);
        }

        return $openChangable;
    }

    // 비밀번호 설정
    public function setPassword($request)
    {
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();
    }

    // 회원 가입
    public function joinUser($request)
    {
        $nowDate = Carbon::now()->toDateString();

        $userInfo = [
            'email' => getEmailAddress($request->get('email')),
            'password' => $request->filled('password') ? bcrypt($request->get('password')) : '',
            'nick' => $request->get('nick'),
            'nick_date' => $nowDate,
            'mailing' => 0,
            'open' => 1,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'login_ip' => $request->ip(),
            'ip' => $request->ip(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'hp' =>  $request->filled('hp') ? trim($request->hp) : null,
            'certify' => $request->filled('certify') ? $request->certify : null,
            'adult' => $request->filled('adult') ? $request->adult : 0,
            'birth' => $request->filled('birth') ? $request->birth : null,
            'sex' => $request->filled('sex') ? $request->sex : null,
            'name' => $request->filled('name') ? cleanXssTags(trim($request->name)) : null,
            'dupinfo' => $request->filled('dupinfo') ? $request->dupinfo : null,
        ];

        // 이메일 인증을 사용할 경우 + 소셜 가입이 아닌 경우
        if(cache('config.email.default')->emailCertify && !session()->get('userFromSocial')) {
            $addUserInfo = [
                'email_certify' => null,
                // 라우트 경로 구분을 위해 /는 제거해 줌.
                'email_certify2' => str_replace("/", "-", bcrypt($request->ip() . Carbon::now()) ),
                'level' => 1,   // 인증하기 전 회원 레벨은 1
            ];
            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        } else {    // 이메일 인증을 사용하지 않을 경우 || 소셜 가입인 경우
            $addUserInfo = [
                'email_certify' => Carbon::now(),
                'level' => cache('config.join')->joinLevel,
            ];

            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        }

        // 회원정보로 유저를 추가한다.
        $lastInsertId = User::insertGetId($userInfo);
        $user = User::find($lastInsertId);

        // 회원 가입 축하 포인트 부여
        insertPoint($user->id, cache("config.join")->joinPoint, '회원가입 축하', '@users', $user->email);

        // Users 테이블의 주 키인 id의 해시 값을 만들어서 저장한다. (게시글에 사용자 번호 노출 방지)
        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));

        $user->save();

        $notice = new Notice();
        // 회원 가입 축하 메일 발송 (인증도 포함되어 있음)
        if(cache('config.email.join')->emailJoinUser) {
            $notice->sendCongratulateJoin($user);
        }
        // 최고관리자에게 회원 가입 알림 메일 발송
        if(cache('config.email.join')->emailJoinSuperAdmin) {
            $notice->sendJoinNotice($user);
        }

        return $user;
    }

    // 회원 정보 수정
    public function updateUserInfo($request)
    {
        $user = auth()->user();
        $openChangable = $this->openChangable($user, Carbon::now());

        // 현재 시간 date type으로 받기
        $nowDate = Carbon::now()->toDateString();

        // 추천인 입력
        $recommendedId = $this->insertRecommend($request, $user);

        $password = $user->password;
        // 비밀번호 변경시
        if($request->password && !Auth::validate(['email' => $user->email, 'password' => $request->password ])) {
            $password = bcrypt($request->password);
        }

        $email = getEmailAddress(trim($request->email));
        $nick = $user->nick;
        $nickDate = $user->nick_date;
        if($request->filled('nick') && $request->nick != $nick) {
            $nick = trim($request->nick);
            $nickDate = $nowDate;
        }
        $toUpdateUserInfo = [
            'email' => $email,
            'password' => $password,
            'id_hashkey' => str_replace("/", "-", bcrypt($user->id)),  // 회원정보수정때마다 id_hashkey를 변경한다.
            'nick' => $nick,
            'nick_date' => $nickDate,
            'homepage' => cleanXssTags($request->homepage),
            'tel' => cleanXssTags($request->tel),
            'addr1' => cleanXssTags($request->addr1),
            'addr2' => cleanXssTags($request->addr2),
            'zip' => preg_replace('/[^0-9]/', '', $request->zip),
            'signature' => trim($request->signature),
            'profile' => trim($request->profile),
            'memo' => trim($request->memo),
            'mailing' => $request->filled('mailing') ? $request->mailing : 0,
            'recommend' => $request->filled('recommend') ? $recommendedId : $user->recommend,
            'hp' =>  $request->filled('hp') ? trim($request->hp) : null,
            'certify' => $request->filled('certify') ? $request->certify : null,
            'adult' => $request->filled('adult') ? $request->adult : 0,
            'birth' => $request->filled('birth') ? $request->birth : null,
            'sex' => $request->filled('sex') ? $request->sex : null,
            'name' => $request->filled('name') ? cleanXssTags(trim($request->name)) : null,
            'dupinfo' => $request->filled('dupinfo') ? $request->dupinfo : null,
        ];

        // 정보공개 체크박스에 체크를 했거나 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if($request->filled('open') || $user->open != $request->get('open')) {
            $toUpdateUserInfo = array_collapse([ $toUpdateUserInfo, [
                'open' => $request->get('open'),
                'open_date' => $nowDate
            ] ]);
        }

        $isEmailChange = $request->get('email') != $user->email;
        // 이메일 인증을 사용하고 이메일이 변경될 경우 이메일 인증을 다시 해야한다.
        if(cache('config.email.default')->emailCertify && $isEmailChange) {
            $toUpdateUserInfo = array_collapse([ $toUpdateUserInfo, [
                'email_certify' => null,
                // 라우트 경로 구분을 위해 /는 제거해 줌.
                'email_certify2' => str_replace("/", "-", bcrypt($request->ip() . Carbon::now()) ),
                'level' => 1,   // 인증하기 전 회원 레벨은 1
            ]]);

            // 이메일 인증 메일 발송
            $notice = new Notice();
            $notice->sendEmailCertify($request->get('email'), $user, $toUpdateUserInfo['nick'], $isEmailChange);
        }

        $folder = getIconFolderName($user->created_at);
        $iconName = getIconName($user->id, $user->created_at);
        $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
        // 아이콘 삭제
        $this->iconDelete($request, $path);
        // 아이콘 업로드
        $this->iconUpload($request, $folder, $iconName, $path);

        return $user->update($toUpdateUserInfo);
    }

    // 소셜 로그인 > 기존 회원 연결 할 때 이메일 인증 통과하도록 함.
    public function updateUserBySocial($user)
    {
        if(cache('config.email.default')->emailCertify && $user->email_certify2) {
            $data = [
                'email_certify' => Carbon::now(),
                'email_certify2' => null,
                'level' => cache('config.join')->joinLevel,
            ];

            $user->update($data);
        }
    }

    // 추천인 입력
    public function insertRecommend($request, $user)
    {
        // 추천인 닉네임 받은 것을 해당 닉네임의 id로 조회
        $recommendedId = 0;
        if($request->filled('recommend')) {
            $recommendedUser = User::where([
                'nick' => $request->recommend,
            ])->first();

            if(is_null($recommendedUser)) {
                throw new Exception('추천인이 존재하지 않습니다. 닉네임을 확인하고 다시 입력해 주세요.');
            }
            if (!auth()->user()->isSuperAdmin() && auth()->user()->id == $recommendedUser->id) {
                throw new Exception('본인을 추천할 수 없습니다.');
            }
            $recommendedId = $recommendedUser->id;

            // 추천인에게 포인트 부여
            insertPoint($recommendedId, cache("config.join")->recommendPoint, $user->email . '의 추천인', '@users', $recommendedUser->email, $user->email . ' 추천');

            $recommendedUser->save();
        }

        return $recommendedId;
    }

    // 아이콘 삭제
    public function iconDelete($request, $path)
    {
        if($request->filled('delIcon') && $request->delIcon) {
            File::delete($path);
        }
    }

    // 아이콘 업로드
    public function iconUpload($request, $folder, $iconName, $path)
    {
        if(isset($request->icon)) {
            if(auth()->user()->level < cache('config.join')->iconLevel) {
                abort(500, '회원아이콘 업로드를 할 수 없는 등급입니다.');
            }
            $file = $request->icon;
            $fileName = $file->getClientOriginalName();
            $dir = '/user/'. $folder;
            $storeFileName = $iconName. '.gif';
            if ( preg_match("/(\.gif)$/i", $fileName) ) {
                // 아이콘 용량이 설정값보다 이하만 업로드 가능
                if (filesize($file) <= cache('config.join')->memberIconSize) {
                    if(File::exists($path)) {
                        //=================================================================\
                        // 090714
                        // gif 파일에 악성코드를 심어 업로드 하는 경우를 방지
                        // 에러메세지는 출력하지 않는다.
                        //-----------------------------------------------------------------
                        $size = getimagesize($file);

                        // 아이콘의 폭 또는 높이가 설정값 보다 작으면 기존에 올라간 아이콘을 삭제하고 업로드하려는 파일로 재등록한다.
                        if ($size[0] <= cache('config.join')->memberIconWidth || $size[1] <= cache('config.join')->memberIconHeight) {
                            File::delete($path);
                            $file->storeAs($dir, $storeFileName);
                        }
                        //=================================================================\

                    } else {
                        $file->storeAs($dir, $storeFileName);
                    }
                } else {
                    abort(500, '회원아이콘을 '.number_format(cache('config.join')->memberIconSize).'바이트 이하로 업로드 해주십시오.');
                }
            } else {
                abort(500, $fileName.'은(는) gif 파일이 아닙니다. 아이콘은 gif 파일만 가능합니다.');
            }
        }
    }

    // 회원 정보 수정에서 소셜 연결 해제
    public function disconnectSocialAccount($request)
    {
        return SocialLogin::where([
            'provider' => $request->provider,
            'social_id' => $request->social_id,
            'user_id' => $request->user_id,
        ])->delete();
    }

    // 회원 정보 수정에서 소셜 계정 연결
    public function connectSocialAccount($userFromSocial, $provider, $request)
    {
        $user = Auth::user();
        // 로그인한 유저가 연결된 소셜 로그인 정보가 있는지 확인
        $socialLogin = SocialLogin::where([
            'provider' => $provider,
            'social_id' => $userFromSocial->getId(),
        ])->first();

        if(is_null($socialLogin)) {
            // 소셜로그인 정보 등록
            session()->put('userFromSocial', $userFromSocial);
            $social = new SocialLogin();
            $socialLogin = $social->insertSocialLogins($request->ip(), $provider);

            // User 모델과 SocialLogin 모델의 관계를 이용해서 social_logins 테이블에 가입한 user_id와 소셜 데이터 저장.
            $user->socialLogins()->save($socialLogin);

            return '소셜 계정이 연결되었습니다.';
        } else {
            // 이미 연결된 계정이라는 안내 메세지 보내 줌
            return '이미 연결된 계정입니다.';
        }
    }

    // 메일 인증 메일주소 변경
    public function changeCertifyEmail($request)
    {
        $beforeEmail = $request->beforeEmail;
        $user = User::where('email', $beforeEmail)->first();
        $user->email = $request->email;
        $user->save();

        // 이메일 인증 메일 발송
        $notice = new Notice();
        $notice->sendEmailCertify($user->email, $user, $user->nick, true);

        return $user->email;
    }

    // 자기소개에 필요한 파라미터 가져오기
    public function getProfileParams($id)
    {
        $user = getUser($id);
        if(auth()->guest()) {
            abort(500, '회원만 이용할 수 있습니다.');
        }
        $loginedUser = auth()->user();
        if(!$loginedUser->open && !$loginedUser->isSuperAdmin() && $loginedUser->id != $user->id) {
            abort(500, '자신의 정보를 공개하지 않으면 다른분의 정보를 조회할 수 없습니다.\\n\\n정보공개 설정은 회원정보수정에서 하실 수 있습니다.');
        }

        if(!$user->open && !$loginedUser->isSuperAdmin() && $loginedUser->id != $user->id) {
            abort(500, '정보공개를 하지 않았습니다.');
        }

        // 가입일과 오늘 날짜와의 차이
        $current = Carbon::now();
        $joinDay = $user->created_at;
        $diffDay = $current->diffInDays($joinDay);

        return [
            'user' => $user,
            'diffDay' => $diffDay
        ];
    }

    // 회원 탈퇴
    public function leaveUser()
    {
        $user = auth()->user();
        if($user->isSuperAdmin()) {
            return '최고 관리자는 탈퇴할 수 없습니다';
        }
        $user->update([
            'leave_date' => Carbon::now()->format('Ymd')
        ]);

        Auth::logout();

        return $user->nick. '님께서는 '. Carbon::now()->format('Y년 m월 d일'). '에 회원에서 탈퇴하셨습니다.';
    }

    // 툴팁 : 메일 보내기 양식
    public function getFormMailParams($request)
    {
        $user = getUser($request->toUser);
        $email = $request->filled('email') ? $request->email : '';
        $decEmail;
        if($email) {
            $decEmail = decrypt($email);
            if( getEmailAddress($decEmail) == '' ) {
                abort(500, '이메일이 올바르지 않습니다.');
            }
        } else {
            abort(500, '이메일이 올바르지 않습니다.');
        }
        $name = $request->filled('name') ? convertText(stripslashes($request->name), true) : $email;

        return [
            'id' => $user->id_hashkey,
            'name' => $name,
        ];
    }

    // 툴팁 : 메일 보내기 실행
    public function sendFormMail($request)
    {
        $to = getUser($request->toUser)->email;
        if (substr_count($to, "@") > 1) {
            abort(500, '한번에 한사람에게만 메일을 발송할 수 있습니다.');
        }
        $name = $request->name;
        $email = $request->email;
        $subject = $request->subject;
        $content = stripslashes($request->content);
        $type = $request->type;
        $files = $request->file ? : [];
        if ($type == 2) {
            $type = 1;
            $content = str_replace("\n", "<br>", $content);
        }

        try {
            Mail::to($to)->queue(new FormMailSend($name, $email, $subject, $content, $type, $files));
        } catch (Exception $e) {
            if($type) {
                $view = 'mail.default.formmail';
            } else {
                $view = 'mail.default.formmail_plain';
            }
            $params = [
                'content' => $content
            ];
            $mailContent = \View::make($view, $params)->render();
            mailer($name, $email, $to, $subject, $mailContent, 1, $files);
        }
    }

    // 비밀번호 조합 정책에 따른 유효성 검사 메세지 추가
    public function addPasswordMessages($messages)
    {
        if(!isset($messages['password.regex'])) {
            $messages['password.regex'] = '';
        }
        if(cache("config.join")->passwordPolicyUpper) {
            $messages['password.regex'] .= '대문자 1개 이상';
        }
        if(cache("config.join")->passwordPolicyNumber) {
            if($messages['password.regex']) {
                $messages['password.regex'] .= ', ';
            }
            $messages['password.regex'] .= '숫자 1개 이상';
        }
        if(cache("config.join")->passwordPolicySpecial) {
            if($messages['password.regex']) {
                $messages['password.regex'] .= ', ';
            }
            $messages['password.regex'] .= '특수문자 1개 이상';
        }
        if($messages['password.regex']) {
            $messages['password.regex'] .= '이 포함된 ';
        }
        $messages['password.regex'] .= cache("config.join")->passwordPolicyDigits. '자리 이상의 문자열로 입력해 주세요.';

        return $messages;
    }

    // 한 페이지에서 한 게시판 및 그룹은 한번만 불러오도록 게시판 리스트를 만들어서 가져다 쓴다.
    public function addBoardList($boardList, $inform)
    {
        if( !array_key_exists($inform['tableName'], $boardList) ) {
            return array_add($boardList, $inform['tableName'], Board::getBoard($inform['tableName'], 'tableName'));
        }

        return $boardList;
    }

    // 한 페이지에서 한 사용자는 한번만 불러오도록 사용자 리스트를 만들어서 가져다 쓴다.
    public function addUserList($userList, $inform)
    {
        if( !array_key_exists($inform['writeUser'], $userList) ) {
            return array_add($userList, $inform['writeUser'], $inform['writeUser'] ? static::getUser($inform['writeUser']) : new User());
        }

        return $userList;
    }

}
