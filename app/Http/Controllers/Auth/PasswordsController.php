<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class PasswordsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRemind()
    {
        $theme = cache('config.theme')->name ? : 'default';
        $skin = cache('config.join')->skin ? : 'default';

        return viewDefault("$theme.users.$skin.password_email");
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function postRemind(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users|max:50',
        ]);

        $email = $request->email;
        $token = str_random(64);

        \DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);

        event(new \App\Events\CreatePasswordRemind($email, $token));

        return alertRedirect(
            trans('auth.passwords.send_reminder')
        );
    }


    /**
     * Display the password reset view for the given token.
     *
     * @param string|null $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReset($token = null)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $skin = cache('config.join')->skin ? : 'default';

        return viewDefault("$theme.users.$skin.password_reset")->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postReset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);

        $token = $request->token;

        $passwordReset = \DB::table('password_resets')->whereToken($token)->first();

        if (! $passwordReset) {
            return $this->respondError(
                trans('auth.passwords.error_wrong_url')
            );
        }

        if($passwordReset->email != $request->email) {
            return $this->respondError(
                trans('auth.passwords.error_wrong_email')
            );
        }

        User::whereEmail($request->email)->first()->update([
            'password' => bcrypt($request->password)
        ]);
        \DB::table('password_resets')->whereToken($token)->delete();

        return alertRedirect(
            trans('auth.passwords.success_reset')
        );
    }

    /**
     * Make an error response.
     *
     * @param     $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function respondError($message)
    {
        return back()->withInput(\Request::only('email'))->withErrors($message);
    }

    /**
     * Make a success response.
     *
     * @param $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function respondSuccess($message)
    {
        return alertRedirect($message);
    }
}
