<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    public $request;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');
        $this->request = $request;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'nick' => 'required|nick_length:2,4|unique:users|alpha_num',
            'tel' => 'nullable|numeric',
            'hp' => 'nullable|numeric',
        ]);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $nowDate = Carbon::now()->toDateString();

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'nick' => $data['nick'],
            'nick_date' => $nowDate,
            'homepage' => isset($data['homepage']) ? $data['homepage'] : null,
            'tel' => isset($data['tel']) ? $data['tel'] : null,
            'hp' => isset($data['hp']) ? $data['hp'] : null,
            'birth' => isset($data['birth']) ? $data['birth'] : null,
            'sex' => isset($data['sex']) ? $data['sex'] : null,
            'signature' => isset($data['signature']) ? $data['signature'] : null,
            'profile' => isset($data['profile']) ? $data['profile'] : null,
            'mailing' => isset($data['mailing']) ? $data['mailing'] : 0,
            'sms' => isset($data['sms']) ? $data['sms'] : 0,
            'open' => isset($data['open']) ? $data['open'] : 0,
            'open_date' => isset($data['open']) ? $nowDate : null,
            'recommend' => isset($data['recommend']) ? $data['recommend'] : null,
            'today_login' => Carbon::now(),
            'login_ip' => $this->request->ip(),
            'ip' => $this->request->ip(),
            'level' => config('gnu.joinLevel'),
            'point' => config('gnu.joinPoint'),

        ]);
    }
}
