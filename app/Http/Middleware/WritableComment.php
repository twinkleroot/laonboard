<?php

namespace App\Http\Middleware;

use Closure;
use App\Common\Util;
use App\Board;

class WritableComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        $user = auth()->user();
        $userPoint = !$user ? 0 : $user->point;
        $board = Board::find($request->boardId);
        // 댓글 쓰기 포인트 설정시 포인트 검사
        if($type == 'create') {
            $tmpPoint = $userPoint > 0 ? $userPoint : 0;
            if($tmpPoint + $board->comment_point < 0 && !session()->get('admin')) {
                $message = '보유하신 포인트('.number_format($userPoint).')가 없거나 모자라서 댓글쓰기('.number_format($board->comment_point).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시 댓글을 써 주십시오.';
                return redirect(route('message'))->with('message', $message);
            }
        }
        // 글 내용 검사
        if( !Util::checkIncorrectContent($request) ) {
            $message = '내용에 올바르지 않은 코드가 다수 포함되어 있습니다.';
            return redirect(route('message'))->with('message', $message);
        }

        if(!$user) {
            // 이름이 누락되어 있는지 확인
            if($request->userName  == '') {
                $message = '이름은 필히 입력하셔야 합니다.';
                return redirect(route('message'))->with('message', $message);
            }
        }

        return $next($request);
    }
}
