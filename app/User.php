<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\SocialLogin;
use App\Point;
use App\Group;
use App\Write;
use Auth;
use DB;
use Cache;
use App\Common\Util;
use App\Notification;
use App\GroupUser;
use Carbon\Carbon;


class User extends Authenticatable
{
    use Notifiable;

    protected $dates = ['today_login', 'email_certify', 'nick_date', 'open_date', ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'nick', 'homepage',
        'level', 'sex', 'birth', 'tel', 'hp', 'certify',
        'adult', 'dupinfo', 'addr1', 'addr2',
        'addr_jibeon', 'signature', 'recommend', 'point',
        'login_ip', 'ip', 'email_certify', 'email_certify2',
        'memo', 'lost_certify', 'mailing', 'sms', 'open',
        'profile', 'memo_call', 'leave_date', 'intercept_date',
        'today_login', 'nick_date', 'open_date', 'zip',
    ];

    public $rulesRegister = [
        'email' => 'required|email|max:255|unique:users',
        'password_confirmation' => 'required',
        'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
    ];

    public $rulesPassword = [
        'password_confirmation' => 'required',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // 소셜 로그인 모델과의 관계 설정
    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    // 게시판 그룹 모델과의 관계 설정
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('id', 'created_at');
    }

    // 포인트 모델과의 관계설정
    public function points()
    {
        return $this->hasMany(Point::class);

    }

    // 포인트 모델과의 관계설정
    public function writes()
    {
        return $this->hasMany(Write::class);

    }

    // 추천인 닉네임 구하기
    public function recommendedPerson($user)
    {
        $recommendedNick = '';
        if(!is_null($user->recommend)) {
            $recommendedNick = User::where([
                'id' => $user->recommend,
            ])->first()->nick;
        }

        return $recommendedNick;
    }

    // 회원 정보 수정 페이지에 전달할 데이터
    public function editFormData($config)
    {
        $user = Auth::user();

        // 정보공개 변경여부
        $openChangable = $this->openChangable($user, Carbon::now());

        $SocialLogin = SocialLogin::where('user_id', $user->id)->get();
        $socials = [
            'naver' => '',
            'google' => '',
            'facebook' => '',
        ];

        foreach($SocialLogin as $sociallogin) {
            if($sociallogin['provider'] == 'naver') {
                $socials['naver'] = $sociallogin['social_id'];
            }
            if($sociallogin['provider'] == 'google') {
                $socials['google'] = $sociallogin['social_id'];
            }
            if($sociallogin['provider'] == 'facebook') {
                $socials['facebook'] = $sociallogin['social_id'];
            }
        }

        $editFormData = [
            'user' => $user,
            'config' => $config,
            'openDate' => Cache::get("config.homepage")->openDate,                      // 정보공개 변경 가능 일
            'nickChangable' => $this->nickChangable($user, Carbon::now(), $config),     // 닉네임 변경여부
            'openChangable' => $openChangable[0],                                       // 정보공개 변경 여부
            'dueDate' => $openChangable[1],                                             // 정보공개 언제까지 변경 못하는지 날짜
            'recommend' => $this->recommendedPerson($user),                             // 추천인 닉네임 id로 가져오기
            'socials' => $socials,                                                      // 소셜에 연결한 정보
        ];

        return $editFormData;
    }

    // 닉네임 변경 가능 여부
    public function nickChangable($user, $current, $config)
    {
        // 현재 시간과 로그인한 유저의 닉네임변경시간과의 차이
        $nickDiff = $current->diffInDays($user->nick_date);
        // 닉네임 변경 여부
        $nickChangable = false;
        if($nickDiff > $config->nickDate) {
            $nickChangable = true;
        }

        return $nickChangable;
    }

    // 정보공개 변경 가능 여부
    public function openChangable($user, $current)
    {
        $config = Cache::get("config.homepage");
        $openChangable = array(false, $current);

        $openDate = $user->open_date;

        if(is_null($openDate)) {
            $openChangable[0] = true;
        } else {
            $openDiff = $current->diffInDays($openDate);
            if($openDiff >= $config->openDate) {
                $openChangable[0] = true;
            }
            $openChangable[1] = $openDate->addDays($config->openDate);
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
    public function userJoin($request, $config)
    {
        $nowDate = Carbon::now()->toDateString();

        $userInfo = [
            'email' => $request->get('email'),
            'password' => $request->has('password') ? bcrypt($request->get('password')) : '',
            'nick' => $request->get('nick'),
            'nick_date' => $nowDate,
            'mailing' => 0,
            'sms' => 0,
            'open' => 0,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'login_ip' => $request->ip(),
            'ip' => $request->ip(),
        ];

        // 이메일 인증을 사용할 경우
        if(Cache::get('config.email.default')->emailCertify) {
            $addUserInfo = [
                'email_certify' => null,
                // 라우트 경로 구분을 위해 /는 제거해 줌.
                'email_certify2' => str_replace("/", "-", bcrypt($request->ip() . Carbon::now()) ),
                'level' => 1,   // 인증하기 전 회원 레벨은 1
            ];
            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        } else {    // 이메일 인증을 사용하지 않을 경우
            $addUserInfo = [
                'email_certify' => Carbon::now(),
                'level' => $config->joinLevel,
            ];

            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        }
        // 회원정보로 유저를 추가한다.
        $user = User::create($userInfo);

        // 회원 가입 축하 포인트 부여
        Point::addPoint([
            'user' => $user,
            'relTable' => '@users',
            'relEmail' => $user->email,
            'relAction' => '회원가입',
            'content' => '회원가입 축하',
            'type' => 'join',
        ]);

        // Users 테이블의 주 키인 id의 해시 값을 만들어서 저장한다. (게시글에 사용자 번호 노출 방지)
        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));

        $user->save();

        $notification = new Notification();
        // 회원 가입 축하 메일 발송 (인증도 포함되어 있음)
        if(Cache::get('config.email.join')->emailJoinUser) {
            $notification->sendCongratulateJoin($user);
        }
        // 최고관리자에게 회원 가입 알림 메일 발송
        if(Cache::get('config.email.join')->emailJoinSuperAdmin) {
            $notification->sendJoinNotification($user);
        }

        return $user;
    }

    // 회원 정보 수정
    public function userInfoUpdate($request, $config)
    {
        $user = Auth::user();
        $openChangable = $this->openChangable($user, Carbon::now());

        // 현재 시간 date type으로 받기
        $nowDate = Carbon::now()->toDateString();

        // 추천인 닉네임 받은 것을 해당 닉네임의 id로 조회
        $recommendedId = '';
        if($request->has('recommend')) {
            $recommendedUser = User::where([
                'nick' => $request->get('recommend'),
            ])->first();

            if(is_null($recommendedUser)) {
                return 'notExistRecommend';
            }
            $recommendedId = $recommendedUser->id;

            // 추천인에게 포인트 부여
            Point::addPoint([
                'user' => $recommendedUser,
                'relTable' => '@users',
                'relEmail' => $recommendedUser->email,
                'relAction' => $user->email . ' 추천',
                'content' => $user->email . '의 추천인',
                'type' => 'recommend',
            ]);

            $recommendedUser->save();
        }

        $toUpdateUserInfo = [
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'id_hashkey' => str_replace("/", "-", bcrypt($user->id)),  // 회원정보수정때마다 id_hashkey를 변경한다.
            'name' => $request->get('name'),
            'nick' => $request->has('nick') ? $request->get('nick') : $user->nick,
            'nick_date' => $request->has('nick') ? $nowDate : $user->nick_date,
            'homepage' => $request->get('homepage'),
            'hp' => $request->get('hp'),
            'tel' => $request->get('tel'),
            'addr1' => $request->get('addr1'),
            'addr2' => $request->get('addr2'),
            'zip' => $request->get('zip'),
            'signature' => $request->get('signature'),
            'profile' => $request->get('profile'),
            'memo' => $request->get('memo'),
            'mailing' => $request->has('mailing') ? $request->get('mailing') : 0,
            'sms' => $request->has('sms') ? $request->get('sms') : 0,
            'recommend' => $request->has('recommend') ? $recommendedId : $user->recommend,
        ];

        // 정보공개 체크박스에 체크를 했거나 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if($request->has('open') || $user->open != $request->get('open')) {
            $toUpdateUserInfo = array_collapse([ $toUpdateUserInfo, [
                'open' => $request->get('open'),
                'open_date' => $nowDate
            ] ]);
        }

        $isEmailChange = $request->get('email') != $user->email;
        // 이메일 인증을 사용하고 이메일이 변경될 경우 이메일 인증을 다시 해야한다.
        if(Cache::get('config.email.default')->emailCertify && $isEmailChange) {
            $toUpdateUserInfo = array_collapse([ $toUpdateUserInfo, [
                'email_certify' => null,
                // 라우트 경로 구분을 위해 /는 제거해 줌.
                'email_certify2' => str_replace("/", "-", bcrypt($request->ip() . Carbon::now()) ),
                'level' => 1,   // 인증하기 전 회원 레벨은 1
            ] ]);

            $user->update($toUpdateUserInfo);
            // 이메일 인증 메일 발송
            $notification = new Notification();
            $notification->sendEmailCertify($request->get('email'), $user, $toUpdateUserInfo['nick'], $isEmailChange);
        } else {
            $user->update($toUpdateUserInfo);
        }

        return 'finishUpdate';
    }

    // 회원 정보 수정에서 소셜 연결 해제
    public function disconnectSocialAccount($request)
    {
        return SocialLogin::where([
            'provider' => $request->get('provider'),
            'social_id' => $request->get('social_id'),
            'user_id' => $request->get('user_id'),
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
            'user_id' => $user->id,
        ])->first();

        if(is_null($socialLogin)) {
            // 소셜로그인 정보 등록
            $socialLogin = new SocialLogin([
                'provider' => $provider,
                'social_id' => $userFromSocial->getId(),
                'social_token' => $userFromSocial->token,
                'ip' => $request->ip(),
            ]);

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
        $notification = new Notification();
        $notification->sendEmailCertify($user->email, $user, $user->nick, true);

        return $user->email;
    }

    // 관리자에서 사용하는 메서드

    // 회원 목록
    public function userList()
    {
        $config = Cache::get("config.homepage");

        $users = DB::table('users as u')
                ->select(DB::raw('
                    u.*,
                    (   select count(gu.id)
                        from group_user as gu
                        where gu.user_id = u.id
                    ) as count_groups'
                ))
                ->orderBy('u.created_at', 'desc')
                ->paginate($config->pageRows);

        return $users;
    }

    // 회원 추가
    public function addUser($data)
    {
        $data = array_except($data, ['_token']);

        $data = Util::exceptNullData($data);

        $data['password'] = bcrypt($data['password']);  // 비밀번호 암호화

        $user = User::create($data);

        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));   // id 암호화
        $user->save();

        return $user;
    }

    // 선택 수정
    public function selectedUpdate($request)
    {
        $idArr = explode(',', $request->get('ids'));
        $openArr = explode(',', $request->get('opens'));
        $mailingArr = explode(',', $request->get('mailings'));
        $smsArr = explode(',', $request->get('smss'));
        $interceptArr = explode(',', $request->get('intercepts'));
        $levelArr = explode(',', $request->get('levels'));

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
    }
}
