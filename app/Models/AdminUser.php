<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User as AppUser;
use File;
use DB;
use Auth;
use Carbon\Carbon;
use App\Services\UserSingleton;

class AdminUser extends Model
{
    protected $dates = ['today_login', 'email_certify', 'nick_date', 'open_date', ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $rulesRegister = [
        'email' => 'required|email|max:255|unique:users',
        'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
        'password_confirmation' => 'required',
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

    public static function getUser($id)
    {
        static $user;
        if (is_null($user) || $user->id != $id) {
            $user = User::find($id);
        }

        return $user;
    }

    // 회원 목록
    public function userList($request)
    {
        $kind = isset($request->kind) ? $request->kind : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $interceptUsers = 0;
        $leaveUsers = 0;

        $query =
            AdminUser::select('users.*',
                DB::raw('
                ( select count(gu.id)
                    from '. env('DB_PREFIX'). 'group_user as gu
                    where gu.user_id = '. env('DB_PREFIX'). 'users.id
                ) as count_groups'
                )
            );

        // 정렬
        if($order) {
            $query = $query->orderBy($order, $direction);
        } else {
            $query = $query->latest();
        }
        // 최고 관리자가 아니면 관리자보다 등급이 같거나 낮은 사람만 조회가능.
        if( !auth()->user()->isSuperAdmin() ) {
            $query = $query->where('level', '<=', auth()->user()->level);
        }

        if($kind) {
            if($kind == 'level') {   // 권한으로 검색
                $query = $query->where($kind, $keyword);
            } else {    // 이메일, 닉네임, IP, 추천인, 가입일, 최근접속일으로 검색
                $query = $query->where($kind, 'like', '%'. $keyword. '%');
            }
        }

        $users = $query->paginate(cache("config.homepage")->pageRows);
        if(isDemo()) {
            foreach($users as $user) {
                if($user->id != auth()->user()->id) {
                    $user->nick = invisible($user->nick);
                    $user->email = invisible($user->email);
                }
            }
        }
        $interceptUsers = $query->whereNotNull('intercept_date')->count();
        $leaveUsers = $query->whereNotNull('leave_date')->count();

        $queryString = "?kind=$kind&keyword=$keyword&page=". $users->currentPage();

        return [
            'users' => $users,
            'interceptUsers' => $interceptUsers,
            'leaveUsers' => $leaveUsers,
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
            'queryString' => $queryString,
        ];
    }

    // 회원 추가
    public function storeUser($request)
    {
        $nowDate = Carbon::now()->toDateString();

        $data = [
            'email' => $request->email,
            'name' => cleanXssTags($request->name),
            'password' => bcrypt($request->password),
            'nick' => trim($request->nick),
            'nick_date' => $nowDate,
            'level' => $request->level,
            'point' => $request->point,
            'homepage' => cleanXssTags($request->homepage),
            'hp' => hyphenHpNumber($request->hp),
            'tel' => cleanXssTags($request->tel),
            'certify' => !$request->certify_signal ? '' : $request->certify,
            'email_certify' => Carbon::now(),
            'adult' => $request->adult,
            'addr1' => cleanXssTags($request->addr1),
            'addr2' => cleanXssTags($request->addr2),
            'zip' => preg_replace('/[^0-9]/', '', $request->zip),
            'mailing' => $request->mailing,
            // 'sms' => $request->sms,
            'open' => $request->open,
            'signature' => trim($request->signature),
            'profile' => trim($request->profile),
            'memo' => trim($request->memo),
            'leave_date' => $request->leave_date,
            'intercept_date' => $request->intercept_date,
            'ip' => $request->ip(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        if($request->open) {
            $data = array_add($data, 'open_date', $nowDate);
        }

        $lastInsertId = AdminUser::insertGetId($data);
        $user = AdminUser::find($lastInsertId);

        $appUser = new AppUser();
        // 추천인 입력
        $recommendedId = $appUser->insertRecommend($request, $user);
        if($recommendedId) {
            $user->recommend = $recommendedId;
        }
        // 아이콘 업로드
        $folder = getIconFolderName($user->created_at);
        $iconName = getIconName($user->id, $user->created_at);
        $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
        $appUser->iconUpload($request, $folder, $iconName, $path);

        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));   // id 암호화
        $user->save();

        return $lastInsertId;
    }

    public function editParams($user, $id)
    {
        // 회원아이콘 경로
        $folder = getIconFolderName($user->created_at);
        $iconName = getIconName($user->id, $user->created_at);
        $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
        $url = '/storage/user/'. $folder. '/'. $iconName. '.gif';

        $appUser = new AppUser();
        $recommend = $appUser->recommendedPerson($user);

        return [
            'user' => $user,
            'id' => $id,
            'iconPath' => $path,
            'iconUrl' => $url,
            'recommend' => $recommend,
            'type' => 'update',
        ];
    }

    // 회원 정보 수정
    public function updateUserInfo($request, $id)
    {
        $user = getUser($id);

        $password = $user->password;
        // 비밀번호 변경시
        if($request->change_password && !Auth::validate(['email' => $user->email, 'password' => $request->change_password ])) {
            $password = bcrypt($request->change_password);
        }

        $nowDate = Carbon::now()->toDateString();

        $appUser = new AppUser();
        // 추천인 입력
        $recommendedId = $appUser->insertRecommend($request, $user);

        $toUpdateUserInfo = [
            'name' => cleanXssTags($request->name),
            'password' => $password,
            'nick' => $request->filled('nick') ? trim($request->nick) : $user->nick,
            'nick_date' => $request->filled('nick') != $user->nick ? $nowDate : $user->nick_date,
            'level' => $request->level,
            'homepage' => cleanXssTags($request->homepage),
            'hp' => hyphenHpNumber($request->hp),
            'tel' => cleanXssTags($request->tel),
            'certify' => !$request->certify_signal ? '' : $request->certify,
            'adult' => $request->adult,
            'addr1' => cleanXssTags($request->addr1),
            'addr2' => cleanXssTags($request->addr2),
            'zip' => preg_replace('/[^0-9]/', '', $request->zip),
            'mailing' => $request->mailing,
            // 'sms' => $request->sms,
            'open' => $request->open,
            'signature' => trim($request->signature),
            'profile' => trim($request->profile),
            'memo' => trim($request->memo),
            'leave_date' => $request->leave_date,
            'intercept_date' => $request->intercept_date,
            'recommend' => $request->filled('recommend') ? $recommendedId : $user->recommend,
        ];

        // 정보공개 체크박스에 체크를 했거나 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if($request->filled('open') || $user->open != $request->open) {
            $toUpdateUserInfo = array_collapse([ $toUpdateUserInfo, [
                'open' => $request->open,
                'open_date' => $nowDate
            ] ]);
        }

        // 아이콘 업로드
        $folder = getIconFolderName($user->created_at);
        $iconName = getIconName($user->id, $user->created_at);
        $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
        // 아이콘 삭제
        $appUser->iconDelete($request, $path);
        // 아이콘 업로드
        $appUser->iconUpload($request, $folder, $iconName, $path);

        $user->update($toUpdateUserInfo);
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
            $user = AdminUser::find($id);

            if(!is_null($user)) {
                $user->update([
                    'open' => $openArr[$index] == '1' ? 1 : 0,
                    'mailing' => $mailingArr[$index] == '1' ? 1 : 0,
                    'sms' => $smsArr[$index] == '1' ? 1 : 0,
                    'intercept_date' => $interceptArr[$index] == 1 ? Carbon::now()->format('Ymd') : null ,
                    'level' => $levelArr[$index],
                ]);
                $index++;
            } else {
                abort('500', '정보를 수정할 회원이 존재하지 않습니다. 회원이 잘 선택 되었는지 확인해 주세요.');
            }
        }
    }

    // 선택 삭제
    public function deleteUser($request)
    {
        // 회원자료는 정보만 없앤 후 아이디는 보관하여 다른 사람이 사용하지 못하도록 함
        foreach(explode(',', $request->ids) as $id) {
            AdminUser::find($id)
            ->update([
                'password' => '',
                'level' => 1,
                'homepage' => '',
                'tel' => '',
                'hp' => '',
                'zip' => '',
                'addr1' => '',
                'addr2' => '',
                'birth' => '',
                'sex' => '',
                'signature' => '',
                'memo' => Carbon::now()->format("Ymd"). ' 삭제함',
            ]);
            // 포인트 테이블에서 삭제
            Point::where('user_id', $id)->delete($id);
            // 그룹접근가능 삭제
            GroupUser::where('user_id', $id)->delete($id);
            // 쪽지 삭제
            Memo::where('send_user_id', $id)->orWhere('recv_user_id', $id)->delete($id);
            // 스크랩 삭제
            Scrap::where('user_id', $id)->delete($id);
            // 관리권한 삭제
            ManageAuth::where('user_id', $id)->delete($id);
            // 그룹관리자인 경우 그룹관리자를 공백으로
            $user = getUser($id);
            Group::where('admin', $user->email)->update([ 'admin' => '' ]);
            // 게시판관리자인 경우 게시판관리자를 공백으로
            Board::where('admin', $user->email)->update([ 'admin' => '' ]);
            // 아이콘 삭제
            $appUser = new AppUser();
            $folder = getIconFolderName($user->created_at);
            $iconName = getIconName($user->id, $user->created_at);
            $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
            $appUser->iconDelete($request, $path);
        }
    }
}
