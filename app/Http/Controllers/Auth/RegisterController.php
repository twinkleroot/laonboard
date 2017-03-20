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
        return Validator::make($data, User::$rulesRegister);
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
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'nick' => $data['nick'],
            'nick_date' => $nowDate,
            'mailing' => 1,
            'sms' => 1,
            'open' => 1,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'login_ip' => $this->request->ip(),
            'ip' => $this->request->ip(),
            'level' => config('gnu.joinLevel'),
            'point' => config('gnu.joinPoint'),
        ]);
    }

}
