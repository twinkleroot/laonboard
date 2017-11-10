<?php

namespace Modules\Certify\Http\Middleware;

use Closure;

class CheckCert
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
        $boardName = $request->segments()[1];
        $board = app()->tagged('board')[0]::getBoard($boardName, 'table_name');

        $userCertify = auth()->check() ? auth()->user()->certify : '';
        $userAdult = auth()->check() ? auth()->user()->adult : 0;

        // 본인 확인을 사용한다면
        if(cache('config.cert')->certUse && !session()->get('admin')) {
            // 인증된 회원만 가능
            if($board->use_cert != 'not-use' && !auth()->check()) {
                return alertRedirect('이 게시판은 본인확인 하신 회원님만 접근이 가능합니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.', '/login?nextUrl='. $request->getRequestUri());
            }
            if($board->use_cert == 'cert' && !$userCertify) {
                return alertRedirect('이 게시판은 본인확인 하신 회원님만 접근이 가능합니다.\\n\\n회원정보 수정에서 본인확인을 해주시기 바랍니다.');
            }
            if($board->use_cert == 'adult' && !$userAdult) {
                return alertRedirect('이 게시판은 본인확인으로 성인인증 된 회원님만 접근이 가능합니다.\\n\\n현재 성인인데 접근이 안된다면 회원정보 수정에서 본인확인을 다시 해주시기 바랍니다.');
            }
            if($board->use_cert == 'hp-cert' && $userCertify != 'hp') {
                return alertRedirect('이 게시판은 휴대폰 본인확인 하신 회원님만 접근이 가능합니다.\\n\\n회원정보 수정에서 휴대폰 본인확인을 해주시기 바랍니다.');
            }
            if($board->use_cert == 'hp-adult' && (!$userAdult || $userCertify != 'hp')) {
                return alertRedirect('이 게시판은 휴대폰 본인확인으로 성인인증 된 회원님만 접근이 가능합니다.\\n\\n현재 성인인데 접근이 안된다면 회원정보 수정에서 휴대폰 본인확인을 다시 해주시기 바랍니다.');
            }
        }

        return $next($request);
    }
}
