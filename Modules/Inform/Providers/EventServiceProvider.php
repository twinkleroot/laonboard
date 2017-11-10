<?php

namespace Modules\Inform\Providers;

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
        // 사용자 메뉴에 알림 추가
        \Modules\Inform\Events\AddNotificationMenu::class => [
            \Modules\Inform\Listeners\AddNotificationMenuListener::class,
        ],
        // 글쓰기 후 알림 전송
        \Modules\Inform\Events\SendInformAboutWrite::class => [
            \Modules\Inform\Listeners\SendInformAboutWriteListener::class,
        ],
        // 댓글쓰기 후 알림 전송
        \Modules\Inform\Events\SendInformAboutComment::class => [
            \Modules\Inform\Listeners\SendInformAboutCommentListener::class,
        ],
        // 유효기간이 만료된 알림 삭제
        \Modules\Inform\Events\DeleteExpireInforms::class => [
            \Modules\Inform\Listeners\DeleteExpireInformsListener::class,
        ],
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
