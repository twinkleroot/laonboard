<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Cache;
use DB;
use Exception;
use Carbon\Carbon;
use App\User;
use App\Board;
use App\Point;
use App\Group;
use App\BoardNew;

class Comment
{
    // 댓글 데이터
    public function getCommentsParams($writeModel, $boardId, $writeId, $request)
    {
        $comments = $writeModel->where(['parent' => $writeId, 'is_comment' => 1])
                ->orderBy('comment')->orderBy('comment_reply')->get();

        foreach($comments as $comment) {
            // 답변, 수정, 삭제 가능여부 기록
            $editable = $this->getCommentAuth($comment, $writeModel, $boardId, $writeId);
            $comment->isReply = $editable['isReply'];
            $comment->isEdit = $editable['isEdit'];
            $comment->isDelete = $editable['isDelete'];
            $comment->user_id = encrypt($comment->user_id);     // 라라벨 기본 지원 encrypt

            // 검색어 색깔 다르게 표시
            if($request->has('keyword')) {
                $comment->content = searchKeyword($request->keyword, $comment->content);
            }
        }


        return [
            'comments' => $comments,
        ];
    }

    // 댓글의 답변, 수정, 삭제 권한 검사
    public function getCommentAuth($comment, $writeModel, $boardId, $writeId)
    {
        $isEdit = 1;
        $isDelete = 1;

        $board = Board::find($boardId);
        $user = auth()->user();
        $commentUser = $comment->user_id == 0 ? '' : User::find($comment->user_id);
        if( !is_null($user) ) {
            if ($user->isSuperAdmin()) {// 최고관리자 통과
                ;
            } else if ($user->isGroupAdmin(Group::find($board->group_id))) { // 그룹관리자
                if ($user->level < $commentUser->level)  { // 자신의 레벨이 글쓴이의 레벨보다 작다면
                    $isEdit = 0;
                    $isDelete = 0;
                }
            } else if ($user->isBoardAdmin($board)) { // 게시판관리자이면
                if ($user->level < $commentUser->level) { // 자신의 레벨이 글쓴이의 레벨보다 작다면
                    $isEdit = 0;
                    $isDelete = 0;
                }
            } else if (!session()->get('admin')) { // 관리자가 아닌 회원인 경우
                if ($user->id != $comment->user_id) {
                    $isEdit = 0;
                    $isDelete = 0;
                }
            }
        } else {    // 비회원일 경우
            $isEdit = 0;
            // 댓글을 비회원이 작성했을 경우
            if(!$commentUser) {
                $isDelete = 1;
            } else {
                $isDelete = 0;
            }
        }

        $cnt = $writeModel->where('comment_reply', 'like', $comment->comment_reply)
                    ->where('id', '<>', $comment->id)
                    ->where([
                        'parent' => $writeId,
                        'comment' => $comment->comment,
                        'is_comment' => 1,
                    ])
                    ->count('id');

        if($cnt && !session()->get('admin')) {
            $isEdit = 0;
            $isDelete = 0;
        }

        $isReply = strlen($comment->comment_reply) != 5 ? 1 : 0;

        return [
            'isReply' => $isReply,
            'isEdit' => $isEdit,
            'isDelete' => $isDelete,
        ];
    }

    // 댓글 생성
    public function storeComment($writeModel, $request)
    {
        $board = Board::find($request->boardId);
        $point = new Point();
        $write = $writeModel->find($request->writeId);  // 원 글
        $writeId = $write->id;

        $tmpComment = 0;
        $tmpCommentReply = '';
        $comment = null;
        // 댓글의 댓글일 때
        if($request->has('commentId')) {
            $comment = $writeModel->where('id', $request->commentId)->first();   // 원 댓글

            if( is_null($comment) ) {
                throw new Exception('답변할 댓글이 없습니다.\\n\\n답변하는 동안 댓글이 삭제되었을 수 있습니다.');
            }
            if(strlen($comment->comment_reply) == 5) {
                throw new Exception('더 이상 답변하실 수 없습니다.\\n\\n답변은 5단계 까지만 가능합니다.');
            }

            $tmpComment = $comment->comment;
            $tmpCommentReply = $this->getCommentReplyValue($writeModel, $write, $comment, $board->reply_order);

        } else {    // 첫 번째 단계의 댓글일 때
            $max = $writeModel->where(['parent' => $writeId, 'is_comment' => 1])->max('comment');
            $tmpComment = $max + 1;
        }

        $user = auth()->user();
        $userId = 0;    // $userId가 0이면 비회원
        $name = '';
        $password = '';
        $email = null;
        $homepage = null;
        // 회원 글쓰기 일 때
        if($user) {
            // 실명을 사용할 때
            if($board->use_name && $user->name) {
                $name = $user->name;
            } else {
                $name = $user->nick;
            }

            $userId = $user->id;
            $password = $user->password;
            $email = $user->email;
            $homepage = $user->homepage;
        } else {
            $name = $request->userName;
            $password = bcrypt($request->password);
        }

        $insertData = [
            'num' => $write->num,
            'reply' => $write->reply,
            'parent' => $write->id,
            'is_comment' => 1,
            'comment' => $tmpComment,
            'comment_Reply' => $tmpCommentReply,
            'ca_name' => $write->ca_name,
            'content' => trim($request->content),      // 필터 한번 거쳐야 함
            'hit' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'ip' => $request->ip(),
            'user_id' => $userId,
            'password' => $password,
            'name' => $name,
            'email' => $email,
            'homepage' => $homepage,
            'option' => $request->has('secret') ? $request->secret : null,
            'extra_1' => $request->has('extra1') ? $request->extra1 : null,
            'extra_2' => $request->has('extra2') ? $request->extra2 : null,
            'extra_3' => $request->has('extra3') ? $request->extra3 : null,
            'extra_4' => $request->has('extra4') ? $request->extra4 : null,
            'extra_5' => $request->has('extra5') ? $request->extra5 : null,
            'extra_6' => $request->has('extra6') ? $request->extra6 : null,
            'extra_7' => $request->has('extra7') ? $request->extra7 : null,
            'extra_8' => $request->has('extra8') ? $request->extra8 : null,
            'extra_9' => $request->has('extra9') ? $request->extra9 : null,
            'extra_10' => $request->has('extra10') ? $request->extra10 : null,
        ];

        $writeModel->insert($insertData);

        // 포인트 부여(댓글)
        $newCommentId = DB::getPdo()->lastInsertId();   // 마지막에 삽입한 행의 id 값 가져오기
        $relAction = '댓글';
        $content = $board->subject. ' '. $writeId. '-'. $newCommentId. ' 댓글쓰기';
        $point->insertPoint($userId, $board->comment_point, $content, $board->table_name, $newCommentId, $relAction);

        // 원글에 댓글수 증가 & 마지막 시간 반영
        $writeModel->where('id', $writeId)
        ->update([
            'comment' => $write->comment + 1,
            'updated_at' => Carbon::now(),
        ]);

        // 새글 Insert
        BoardNew::Create([
            'board_id' => $board->id,
            'write_id' => $newCommentId,
            'write_parent' => $writeId,
            'created_at' => Carbon::now(),
            'user_id' => $userId
        ]);

        // 댓글 1 증가
        $board->update(['count_comment' => $board->count_comment + 1]);

        // 메인 최신글 캐시 삭제
        deleteCache('main', $board->table_name);

        return $newCommentId;
    }

    // 댓글 단계 구하는 로직
    private function getCommentReplyValue($writeModel, $write, $comment, $replyOrder)
    {
        $commentReplyLength = strlen($comment->comment_reply) + 1;
        if ($replyOrder == 1) {
            $baginReplyChar = 'A';
            $endReplyChar = 'Z';
            $replyNumber = 1;
            $query = $writeModel->selectRaw("MAX(SUBSTRING(comment_reply, ". $commentReplyLength. ", 1)) as reply")
                    ->where('parent', $write->id)
                    ->where('comment', $comment->comment)
                    ->whereRaw("SUBSTRING(comment_reply, ". $commentReplyLength. ", 1) <> ''");
        } else {
            $baginReplyChar = 'Z';
            $endReplyChar = 'A';
            $replyNumber = -1;
            $query = $writeModel->selectRaw("MIN(SUBSTRING(comment_reply, ". $commentReplyLength. ", 1)) as reply")
                    ->where('parent', $write->id)
                    ->where('comment', $comment->comment)
                    ->whereRaw("SUBSTRING(comment_reply, ". $commentReplyLength. ", 1) <> ''");

        }
        if ($comment->comment_reply) {
            $query->where('comment_reply', 'like', $comment->comment_reply.'%');
        }
        $result = $query->first(); // 쿼리 실행 결과

        if (is_null($result->reply)) {
            $replyChar = $baginReplyChar;
        } else if ($result->reply == $endReplyChar) { // A~Z은 26 입니다.
            return '더 이상 답변하실 수 없습니다.\\n답변은 26개 까지만 가능합니다.';
        } else {
            $replyChar = chr(ord($result->reply) + $replyNumber);
        }

        return $comment->comment_reply . $replyChar;
    }

    // 댓글 수정
    public function updateComment($writeModel, $request)
    {
        $board = Board::find($request->boardId);
        $commentId = $request->commentId;
        $comment = $writeModel->find($commentId);
        $writeId = $comment->parent;
        $option = $request->has('secret') ? $request->secret : null;
        $ip = !session()->get('admin') ? $request->ip() : $comment->ip;

        $result = $writeModel->where('id', $commentId)->update([
            'content' => $request->content,
            'option' => $option,
            'ip' => $ip,
        ]);

        // 메인 최신글 캐시 삭제
        deleteCache('main', $board->table_name);

        return $result;
    }

    // 댓글 삭제
    public function deleteComment($writeModel, $boardId, $commentId)
    {
        $board = Board::find($boardId);
        $point = new Point();
        $writeModel->setTableName($board->table_name);
        $comment = $writeModel->find($commentId);
        $write = $writeModel->find($comment->parent);

        // 댓글 포인트 삭제, 부여되었던 포인트 삭제 및 조정 반영
        if($comment->user_id) {
            $delPointResult = $point->deleteWritePoint($writeModel, $board->id, $commentId);
            if($delPointResult <= 0) {
                return '정상적으로 댓글을 삭제하는데 실패하였습니다.(포인트 삭제)';
            }
        }
        // 댓글 삭제
        if(!$writeModel->where('id', $commentId)->delete()) {
            return '정상적으로 댓글을 삭제하는데 실패하였습니다.(댓글 삭제)';
        }

        $updateWriteAboutComment = $writeModel->where('id', $write->id)->update([
            'updated_at' => Carbon::now(),      // 원글의 최근 변경 시간 업데이트
            'comment' => $write->comment - 1    // 원글의 댓글 숫자 감소
        ]);
        if(!$updateWriteAboutComment) {
            return '정상적으로 원글의 정보를 변경하는데 실패하였습니다.';
        }

        // 게시판의 댓글 개수 감소
        if(!$board->update(['count_comment' => $board->count_comment - 1])) {
            return '정상적으로 게시판의 정보를 변경하는데 실패하였습니다.';
        }

        // 새글 삭제
        if(!BoardNew::where(['board_id' => $board->id, 'write_id' => $commentId])->delete()) {
            return '정상적으로 새글을 삭제하는데 실패하였습니다.';
        }

        // 메인 최신글 캐시 삭제
        deleteCache('main', $board->table_name);
    }

}
