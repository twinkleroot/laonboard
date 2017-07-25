<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ConfigRegister::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // 관리자 메뉴구성
        'admin.menu' => \App\Http\Middleware\AdminMenu::class,
        // 관리자만 접근가능할 때 사용
        'admin' => \App\Http\Middleware\IsAdmin::class,
        // 최고관리자만 접근가능할 때 사용
        'super' => \App\Http\Middleware\IsSuperAdmin::class,
        // 유저의 레벨과 게시판 마다의 레벨 기준을 비교해서 접근 가능 여부 판단
        'level.board' => \App\Http\Middleware\CheckBoardLevel::class,
        // 댓글/글 수정, 삭제가 가능한지 검사
        'updatable.deletable.write' => \App\Http\Middleware\UpdatableAndDeletableWrite::class,
        // 글 답변이 가능한지 검사
        'writable.reply' => \App\Http\Middleware\WritableReply::class,
        // 댓글 쓰기가 가능한지 검사
        'writable.comment' => \App\Http\Middleware\WritableComment::class,
        // 댓글 삭제가 가능한지 검사
        'deletable.comment' => \App\Http\Middleware\DeletableComment::class,
        // 해당 항목의 유효 여부 검사
        'store.write' => \App\Http\Middleware\VerifyBoardWrite::class,
        'valid.write' => \App\Http\Middleware\CheckValidWrite::class,
        'valid.board' => \App\Http\Middleware\CheckValidBoard::class,
        'valid.user' => \App\Http\Middleware\CheckValidUser::class,
        // 글 보기 할 때 요청한 경로의 글이 댓글이면 원글을 보여주도록
        'comment.view.parent' => \App\Http\Middleware\CommentViewParent::class,
        // 비밀글 조회 전 체크할 내용
        'secret.board' => \App\Http\Middleware\CheckSecretView::class,
        'can.action.write.immediately' => \App\Http\Middleware\CanActionWriteImmediately::class,
        'can.delete.comment.immediately' => \App\Http\Middleware\CanDeleteCommentImmediately::class,
        // RSS 조회 가능한지 검사
        'rss' => \App\Http\Middleware\CheckRss::class,
        // 메일 보내기가 가능한지 검사
        'form.mail' => \App\Http\Middleware\CheckFormMail::class,
        'send.mail' => \App\Http\Middleware\CheckSendMail::class,

    ];
}
