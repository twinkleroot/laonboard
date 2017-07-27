<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Admin\Point;
use App\Admin\Group;
use App\Admin\ManageAuth;
use App\Admin\GroupUser;
use App\SocialLogin;
use App\Write;
use App\Memo;
use App\Scrap;
use App\Board;
use App\User as AppUser;
use File;
use DB;
use Carbon\Carbon;

class AdminUser extends Model
{
    protected $table = 'users';

    protected $dates = ['today_login', 'email_certify', 'nick_date', 'open_date', ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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

    // SocialLogin 모델과의 관계 설정
    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    // BoardGroup 모델과의 관계 설정
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('id', 'created_at');
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
            DB::table('users as u')
            ->select(DB::raw('
                u.*,
                (   select count(gu.id)
                    from group_user as gu
                    where gu.user_id = u.id
                ) as count_groups'
            ));

        // 정렬
        if($order) {
            $query = $query->orderBy($order, $direction);
        } else {
            $query = $query->orderBy('u.created_at', 'desc');
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
    public function addUser($request)
    {
        $data = $request->all();
        $data = array_except($data, ['_token']);
        $data = exceptNullData($data);
        $data = array_add($data, 'ip', $request->ip());
        $data['password'] = bcrypt($data['password']);  // 비밀번호 암호화

        $user = AdminUser::create($data);

        if(is_null($user)) {
            abort(500, '회원추가에 실패하였습니다.');
        }

        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));   // id 암호화
        $user->save();

        return $user->id;
    }

    public function editParams($user, $id)
    {
        // 회원아이콘 경로
        $path = storage_path('app/public/user/'. substr($user->email,0,2). '/'). $user->email. '.gif';
        $url = '/storage/user/'. substr($user->email,0,2). '/'. $user->email. '.gif';

        $appUser = new AppUser();
        $recommend = $appUser->recommendedPerson(getUser($id));

        return [
            'user' => $user,
            'id' => $id,
            'iconUrl' => $url,
            'iconPath' => $path,
            'recommend' => $recommend,
        ];
    }

    // 회원 정보 수정
    public function updateUserInfo($request, $id)
    {
        $user = getUser($id);

        $password = $user->password;
        if($request->get('change_password') !== '') {
            $password = bcrypt($request->get('change_password'));
        }

        $nowDate = Carbon::now()->toDateString();

        $appUser = new AppUser();
        // 추천인 입력
        $recommendedId = $appUser->insertRecommend($request, $user);

        $toUpdateUserInfo = [
            'name' => cleanXssTags($request->name),
            'password' => $password,
            'nick' => $request->has('nick') ? trim($request->nick) : $user->nick,
            'nick_date' => $request->has('nick') != $user->nick ? $nowDate : $user->nick_date,
            'level' => $request->level,
            // 'point' => $request->get('point'),	// 포인트 부여 및 차감은 [회원관리 - 포인트관리]에서
            'homepage' => cleanXssTags($request->homepage),
            'hp' => hyphenHpNumber($request->hp),
            'tel' => cleanXssTags($request->tel),
            'certify' => !$request->certify_signal ? '' : $request->certify,
            'adult' => $request->adult,
            'addr1' => cleanXssTags($request->addr1),
            'addr2' => cleanXssTags($request->addr2),
            'zip' => preg_replace('/[^0-9]/', '', $request->zip),
            'mailing' => $request->mailing,
            'sms' => $request->sms,
            'open' => $request->open,
            'signature' => trim($request->signature),
            'profile' => trim($request->profile),
            'memo' => trim($request->memo),
            'leave_date' => $request->leave_date,
            'intercept_date' => $request->intercept_date,
            'recommend' => $request->has('recommend') ? $recommendedId : $user->recommend,
        ];

        // 정보공개 체크박스에 체크를 했거나 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if($request->has('open') || $user->open != $request->open) {
            $toUpdateUserInfo = array_collapse([ $toUpdateUserInfo, [
                'open' => $request->open,
                'open_date' => $nowDate
            ] ]);
        }

        $path = storage_path('app/public/user/'. substr($user->email,0,2). '/'). $user->email. '.gif';
        // 아이콘 삭제
        $appUser->iconDelete($request, $path);
        // 아이콘 업로드
        $appUser->iconUpload($request, $user->email, $path);

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
        $path = storage_path('app/public/user/'. substr($user->email,0,2). '/'). $user->email. '.gif';
        File::delete($path);
        }
    }
}
