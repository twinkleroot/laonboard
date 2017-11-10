<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // 로그인 성공 후 이벤트
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\LoginEventListener::class,
        ],
        // 소셜 로그인 이벤트 : 구글, 페이스북, 트위터, 깃헙 등은 기본 제공.
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \SocialiteProviders\Naver\NaverExtendSocialite::class,	// 네이버
            \SocialiteProviders\Kakao\KakaoExtendSocialite::class,	// 카카오
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \App\Listeners\UsersEventListener::class,
        \App\Listeners\BoardsEventListener::class,
        \App\Listeners\CommentsEventListener::class,
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
    }
}
