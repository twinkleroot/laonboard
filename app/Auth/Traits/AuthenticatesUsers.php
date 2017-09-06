<?php

namespace App\Auth\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cache;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\RedirectsUsers;
use App\User;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $skin = Cache::get('config.join')->skin ? : 'default';

        return viewDefault("user.$skin.login");
    }
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $referUrl = $request->server('HTTP_REFERER');
        $divUrl = explode('nextUrl=', $referUrl);
        if(count($divUrl) > 1) {
            $this->redirectTo = $divUrl[1];
        }
        $user = User::where('email', $request->email)->first();
        $skin = Cache::get('config.join')->skin ? : 'default';
        if($user && Cache::get('config.email.default')->emailCertify && $user->level == 1) {
            $params = [
                'confirm' => '메일로 메일인증을 받으셔야 로그인 가능합니다.\\n다른 메일주소로 변경하여 인증하시려면 취소를 클릭하시기 바랍니다.',
                'redirect' => route('user.email.edit', $request->email),
            ];
            return viewDefault("user.$skin.login_confirm", $params);
        }

        if($user && $user->leave_date) {
            $leaveDate = $user->leave_date;
            $leaveYear = substr($leaveDate, 0, 4);
            $leaveMonth = substr($leaveDate, 4, 2);
            $leaveDay = substr($leaveDate, 6, 2);
            $message = '탈퇴한 아이디이므로 접근하실 수 없습니다.\\n탈퇴일 : '. $leaveYear. '년'. $leaveMonth. '월'. $leaveDay. '일';

            return alertRedirect($message);
        }

        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required', 'password' => 'required',
        ]);
    }
    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->all($this->username(), 'password');
    }
    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->all($this->username(), 'remember'))
            ->withErrors($errors);
    }
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
