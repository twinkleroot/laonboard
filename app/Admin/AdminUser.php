<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\SocialLogin;
use App\Admin\Point;
use App\Admin\Group;
use App\Write;
use DB;
use Cache;
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

        $users = $query->paginate(Cache::get("config.homepage")->pageRows);
        $interceptUsers = $query->whereNotNull('intercept_date')->count();
        $leaveUsers = $query->whereNotNull('leave_date')->count();

        return [
            'users' => $users,
            'interceptUsers' => $interceptUsers,
            'leaveUsers' => $leaveUsers,
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
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
}
