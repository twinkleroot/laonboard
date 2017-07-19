<?php

namespace App\Http\Middleware;

use Closure;

class CheckSendMail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if( !cache("config.email.default")->emailUse ) {
            return alertClose('환경설정에서 \'메일발송 사용\'에 체크하셔야 메일을 발송할 수 있습니다.\\n\\n관리자에게 문의하시기 바랍니다.');
        }
		if( auth()->guest() && cache("config.email.default")->formmailIsMember ) {
            return alertClose('회원만 이용하실 수 있습니다.');
        }

        return $next($request);
    }
}
