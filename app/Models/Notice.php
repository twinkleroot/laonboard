<?php

namespace App\Models;

use Mail;
use Exception;
use App\Mail\WriteNotice;
use App\Mail\JoinNotice;
use App\Mail\CongratulateJoin;
use App\Mail\EmailCertify;

class Notice
{
    // 글쓰기 후 알림 메일 보내기
    public function sendWriteNotice($writeModel, $writeId)
    {
        $board = $writeModel->board;
        $user = auth()->check() ? auth()->user() : new User();
        $boardAdmin = $board->admin;    // 게시판 관리자
        $groupAdmin = $board->group->admin;		// 그룹 관리자
        $superAdmin = cache('config.homepage')->superAdmin;    // 최고 관리자
        $write = $writeModel->find($writeId);   // 작성한 글
        $parentWrite = $write->id == $write->parent ? $write : $writeModel->find($write->parent);   // 원글
        if($write->is_comment) {
            $content = clean('<p>원글<br>'. $parentWrite->subject. '</p><br><p>댓글<br>'. $write->content. '</p>');     // 글 내용
        } else {
            $content = clean($write->content);
        }
        // img 태그가 포함되어 있는 경우 변환 처리
        $content = $this->addPathImgTag($write->content);
        $writeSubject = $write->subject;    // 글 제목
        $name = $write->name;
        $type = '새';
        $tag = '';
        if($write->is_comment) {
            $type = '코멘트';
            $tag = '#comment'. $write->id;
            $writeSubject = '';
        } else if($write->reply) {
            $type = '답변';
        };
        // 메일 제목
        $mailSubject = '['. cache('config.homepage')->title. '] '. $board->subject. ' 게시판에 '. $type. '글이 올라왔습니다.';
        // 게시글 링크 주소
        $linkUrl = route('board.view', ['boardId' => $board->table_name, 'writeId' => $parentWrite->id]). $tag;

        $arrayEmail = [];
        $mailConfig = cache('config.email.board');
        // 최고관리자에게 보내는 메일
        if($mailConfig->emailWriteSuperAdmin && $superAdmin) {
            $arrayEmail[] = $superAdmin;
        }
        // 게시판그룹관리자에게 보내는 메일
        if($mailConfig->emailWriteGroupAdmin && $groupAdmin) {
            $arrayEmail[] = $groupAdmin;
        }
        // 게시판관리자에게 보내는 메일
        if($mailConfig->emailWriteBoardAdmin && $boardAdmin) {
            $arrayEmail[] = $boardAdmin;
        }
        // 원글게시자에게 보내는 메일
        if($mailConfig->emailWriter) {
            $arrayEmail[] = $parentWrite->email;
        }
        // 댓글 쓴 모든이에게 메일 발송이 되어 있다면 (자신에게는 발송하지 않는다)
        if($mailConfig->emailAllCommenter) {
            $commenters = $writeModel
                        ->whereNotIn('email', [$write->email, $user->email, ''])
                        ->where('parent', $write->parent)
                        ->select('email')
                        ->distinct()
                        ->get();
            foreach($commenters as $commenter) {
                $arrayEmail[] = $commenter->email;
            }
        }
         // 옵션에 메일받기가 체크되어 있다면
        if(strstr($parentWrite['option'], 'mail')) {
            $arrayEmail[] = $parentWrite->email;
        }
        // null값과 중복된 메일 주소 제거
        $uniqueEmail = array_values(array_unique(array_filter($arrayEmail)));

        // 현재 사용자가 최고관리자이면 최고관리자에게는 메일을 발송하지 않는다.
        if($user->isSuperAdmin()) {
            $uniqueEmail = array_where($uniqueEmail, function ($value, $key) use($superAdmin) {
                return $value != $superAdmin;
            });
        }

        foreach($uniqueEmail as $to) {
            $toUser = User::where('email', $to)->first();

            try {
                Mail::to($toUser)->queue(new WriteNotice($mailSubject, $writeSubject, $name, $content, $linkUrl));
            } catch (Exception $e) {
                $params = [
                    'subject' => $writeSubject,
                    'name' => $name,
                    'content' => $content,
                    'linkUrl' => $linkUrl,
                ];
                $theme = cache('config.theme')->name ? : 'default';
                $mailPath = "themes.$theme.mails.write_notice";
                $mailPath = view()->exists($mailPath) ? $mailPath : "themes.default.mails.write_notice";
                $mailContent = \View::make($mailPath, $params)->render();

                mailer(
                    cache('config.email.default')->adminEmailName,
                    cache('config.email.default')->adminEmail,
                    $to,
                    $mailSubject,
                    $mailContent
                );
            }

        }
    }

    // 에디터로 업로드한 이미지 경로를 추출해서 내용의 img 태그 부분을 교체한다.
    private function addPathImgTag($content)
    {
        // 에디터로 업로드한 이미지 경로를 추출한다.
        $imgPattern = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";

        preg_match_all($imgPattern, $content, $matches);

        for($i=0; $i<notNullCount($matches[1]); $i++) {
            $divImage1 = explode('.', basename($matches[1][$i]));
            $divImage2 = explode('_', $divImage1[0]);
            $realImageName = str_replace("thumb-", "", $divImage2[0]). '.'. last($divImage1);

            $sourcePath = "src=\"". env('APP_URL'). $matches[1][$i]. "\" style=\"max-width:100%;\"";
            $srcPattern = "/src=[\"']?([^>\"']+){$realImageName}[\"']?[^>]/i";
            $content = preg_replace($srcPattern, $sourcePath, $content);
        }

        return $content;
    }

    // 가입한 회원에게 가입 축하 메일 보내기
    public function sendCongratulateJoin($user)
    {
        $subject = '['. cache('config.homepage')->title. '] 회원가입을 축하드립니다.';
        try {
            Mail::to($user)->queue(new CongratulateJoin($user, $subject));
        } catch (Exception $e) {
            $params = [
                'user' => $user,
                'url' => route('user.email.certify', [
                             'id' => $user->id_hashkey,
                             'crypt' => $user->email_certify2
                         ])
            ];
            $theme = cache('config.theme')->name ? : 'default';
            $mailPath = "themes.$theme.mails.congratulate_join";
            $mailPath = view()->exists($mailPath) ? $mailPath : "themes.default.mails.congratulate_join";
            $mailContent = \View::make($mailPath, $params)->render();

            mailer(
                cache('config.email.default')->adminEmailName,
                cache('config.email.default')->adminEmail,
                $user->email,
                $subject,
                $mailContent
            );
        }
    }

    // 최고관리자에게 회원 가입 알림 메일 보내기
    public function sendJoinNotice($user)
    {
        $subject = '['. cache('config.homepage')->title. '] '. $user->nick. ' 님께서 회원으로 가입하셨습니다.';
        try {
            Mail::to(cache('config.homepage')->superAdmin)->queue(new JoinNotice($user, $subject));
        } catch (Exception $e) {
            $params = [
                'user' => $user,
            ];
            $theme = cache('config.theme')->name ? : 'default';
            $mailPath = "themes.$theme.mails.join_notice";
            $mailPath = view()->exists($mailPath) ? $mailPath : "themes.default.mails.join_notice";
            $mailContent = \View::make($mailPath, $params)->render();

            mailer(
                cache('config.email.default')->adminEmailName,
                cache('config.email.default')->adminEmail,
                cache('config.homepage')->superAdmin,
                $subject,
                $mailContent
            );
        }

    }

    // 회원 정보 수정에서 이메일 변경시 이메일 인증 메일 발송
    public function sendEmailCertify($to, $user, $nick, $isEmailChange)
    {
        $subject = '['. cache('config.homepage')->title. '] 인증확인 메일입니다.';
        try {
            Mail::to($to)->queue(new EmailCertify($user, $nick, $subject, $isEmailChange));
        } catch (Exception $e) {
            $params = [
                'nick' => $nick,
                'isEmailChange' => $isEmailChange,
                'url' => route('user.email.certify', [
                             'id' => $user->id_hashkey,
                             'crypt' => $user->email_certify2,
                         ]),
            ];
            $theme = cache('config.theme')->name ? : 'default';
            $mailPath = "themes.$theme.mails.email_certify";
            $mailPath = view()->exists($mailPath) ? $mailPath : "themes.default.mails.email_certify";
            $mailContent = \View::make($mailPath, $params)->render();

            mailer(
                cache('config.email.default')->adminEmailName,
                cache('config.email.default')->adminEmail,
                $to,
                $subject,
                $mailContent
            );
        }

    }
}
