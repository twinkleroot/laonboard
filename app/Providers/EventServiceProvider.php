<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Event;
use App\Listeners\LoginListener;
use App\Listeners\ViewSecretWrite;
use App\Listeners\BeforeReadListener;
use App\Events\BeforeRead;
use App\Events\CheckSecret;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Naver\NaverExtendSocialite;
use SocialiteProviders\Kakao\KakaoExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // 로그인 성공 후 이벤트
        Login::class => [
            LoginListener::class,
        ],
        // 소셜 로그인 이벤트 : 구글, 페이스북, 트위터, 깃헙 등은 기본 제공.
        SocialiteWasCalled::class => [
            NaverExtendSocialite::class,	// 네이버
            KakaoExtendSocialite::class,	// 카카오
        ],
        // 글 읽기전 조회수 증가/ 포인트 계산하는 이벤트
        'App\Events\BeforeRead' => [
            'App\Listeners\BeforeReadListener',
        ],
        // 다운로드전 다운로드수 증가/ 포인트 계산하는 이벤트
        'App\Events\BeforeDownload' => [
            'App\Listeners\BeforeDownloadListener',
        ],
        // RSS 보기전 검사하는 이벤트
        'App\Events\GetRssView' => [
            'App\Listeners\GetRssViewListener',
        ],
        // 답변글 쓸 수 있는지 검사하는 이벤트
        'App\Events\WriteReply' => [
            'App\Listeners\WriteReplyListener',
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        // 댓글 쓰기/수정전 검사하는 이벤트
        \App\Listeners\CommentListener::class,
    ];


    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    // public function boot(DispatcherContract $events)
    public function boot()
    {
        parent::boot();

        // Event::listen(
        //     \Illuminate\Auth\Events\Login::class,
        //     \App\Listeners\LoginSuccessful::class
        // );
    }
}
