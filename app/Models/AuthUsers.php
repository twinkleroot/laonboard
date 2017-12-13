<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cache;

trait AuthUsers
{
    use \Illuminate\Foundation\Auth\AuthenticatesUsers;
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $theme = cache('config.theme')->name ? : 'default';
        $skin = cache('config.join')->skin ? : 'default';

        return viewDefault("$theme.users.$skin.login");
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
        if(notNullCount($divUrl) > 1) {
            $this->redirectTo = $divUrl[1];
        }
        $user = User::where('email', $request->email)->first();
        if($user && cache('config.email.default')->emailCertify && $user->level == 1) {
            $params = [
                'confirm' => '메일로 메일인증을 받으셔야 로그인 가능합니다.\\n다른 메일주소로 변경하여 인증하시려면 취소를 클릭하시기 바랍니다.',
                'redirect' => route('user.email.edit', $request->email),
            ];
            return view("common.confirm_and_history_back", $params);
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

}
