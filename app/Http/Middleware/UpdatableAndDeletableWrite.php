<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use Hash;
use App\Board;
use App\Group;
use App\User;
use App\Write;

class UpdatableAndDeletableWrite
{
    /**
     * 댓글, 글 수정/삭제 전 체크 로직
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $board = Board::find($request->boardId);
        $writeModel = new Write($request->boardId);
        $writeModel->setTableName($board->table_name);

        $message = '';
        $action = '수정';
        $target = '글';
        $id = 0;
        $isDelete = strpos($request->getRequestUri(), 'delete');
        $isComment = $request->commentId ? : 0;
        // 수정인지 삭제인지
        if($isDelete) {
            $action = '삭제';
        }
        // 댓글 수정/삭제일 경우
        if($isComment) {
            $target = '댓글';
            $id = $request->commentId;
        } else {
            $id = $request->writeId;
        }
        $write = $writeModel->find($id);
        $writeUser = ( !is_null($write) && $write->user_id == 0) ? '' : User::find($write->user_id);
        if( !is_null($user) ) {
            if ($user->isSuperAdmin()) {// 최고관리자 통과
                ;
            } else if ($user->isGroupAdmin(Group::find($board->group_id))) { // 그룹관리자
                if ($user->level < $writeUser->level)  { // 자신의 레벨이 글쓴이의 레벨보다 작다면
                    $message = '그룹관리자의 권한보다 높은 권한의 회원이 작성한 '. $target. '은 '. $action. '할 수 없습니다.';
                }
            } else if ($user->isBoardAdmin($board)) { // 게시판관리자이면
                if ($user->level < $writeUser->level) { // 자신의 레벨이 글쓴이의 레벨보다 작다면
                    $message = '게시판관리자의 권한보다 높은 권한의 회원이 작성한 '. $target. '은 '. $action. '할 수 없습니다.';
                }
            } else if ($user) { // 로그인한 유저인 경우
                if ($user->id != $write->user_id) {
                    $message = '자신의 '. $target. '이 아니므로 '. $action. '할 수 없습니다.';
                }

                if($isDelete) {
                    $this->checkReply($writeModel, $write);
                }
                $this->checkComment($writeModel, $write, $board, $isDelete);
            }
        } else {    // 비회원일 경우
            if($isComment) {
                if(!Hash::check($request->password, $write->password)) {
                    if(!$isDelete) {
                        $message = $target. '을 수정할 권한이 없습니다.';
                    }
                }
            } else {
                if($write->user_id) {
                    $message = '로그인 후 수정하세요.';
                    $redirect = route('login'). '?url='. route('board.view', ['boardId' => $board->id, 'writeId' => $write->id]);
                    return alertRedirect($message, $redirect);
                }
            }
        }

        if($isComment) {
            $cnt = $writeModel->where('comment_reply', 'like', $write->comment_reply)
                ->where('id', '<>', $request->commentId)
                ->where([
                    'parent' => $request->writeId,
                    'comment' => $write->comment,
                    'is_comment' => 1,
                ])->count('id');

            if($cnt && !session()->get('admin')) {
                $message = '이 댓글와 관련된 답변댓글이 존재하므로 수정 할 수 없습니다.';
            }
        }

        if($message != '') {
            return alert($message);
        }

        return $next($request);
    }

    // 해당 글에 답변글이 달려 있는지 확인한다.
    public function checkReply($writeModel, $write)
    {
        $replyCount = $writeModel->where('reply', 'like', $write->reply.'%')
                        ->where('id', '<>', $write->id)
                        ->where(['num' => $write->num, 'is_comment' => 0])
                        ->count('id');
        if($replyCount > 0 && !session()->get('admin')) {
            abort(500, '이 글과 관련된 답변글이 존재하므로 삭제 할 수 없습니다.\\n\\n우선 답변글부터 삭제하여 주십시오.');
        }
    }

    // 해당 글에 댓글이 달려 있는지 확인한다.
    public function checkComment($writeModel, $write, $board, $isDelete)
    {
        $commentCount = $writeModel->where('user_id', '<>', $write->user_id)
                        ->where(['parent' => $write->id, 'is_comment' => 1])
                        ->count('id');
        if($isDelete && $commentCount >= $board->count_delete) {
            abort(500, '이 글과 관련된 댓글이 존재하므로 삭제할 수 없습니다.\\n\\댓글이 '. $board->count_delete. '건 이상 달린 원글은 삭제할 수 없습니다.');
        }
        if(!$isDelete && $commentCount >= $board->count_modify) {
            abort(500, '이 글과 관련된 댓글이 존재하므로 수정할 수 없습니다.\\n\\댓글이 '. $board->count_modify. '건 이상 달린 원글은 수정할 수 없습니다.');
        }
    }

}
