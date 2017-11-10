<?php

namespace App\Modules\Providers;

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
        // 상단 로고
        \App\Modules\Events\AddLogo::class => [
            \App\Modules\Listeners\AddLogoListener::class,
        ],
        // 상단 전체검색 바
        \App\Modules\Events\AddSearchBar::class => [
            \App\Modules\Listeners\AddSearchBarListener::class,
        ],
        // 상단 메뉴 바
        \App\Modules\Events\AddMenuBar::class => [
            \App\Modules\Listeners\AddMenuBarListener::class,
        ],
        // 사용자 메뉴에 알림 추가
        \App\Modules\Events\AddNotificationMenu::class => [
            \App\Modules\Listeners\AddNotificationMenuListener::class,
        ],
        // 최신글 리스트
        \App\Modules\Events\AddLatestList::class => [
            \App\Modules\Listeners\AddLatestListListener::class,
        ],
        // Copyright
        \App\Modules\Events\AddCopyright::class => [
            \App\Modules\Listeners\AddCopyrightListener::class,
        ],
        // 회원정보수정 : 이름과 휴대폰 입력 부분 기본 양식
        \App\Modules\Events\AddToEditUserInfo::class => [
            \App\Modules\Listeners\AddToEditUserInfoListener::class,
        ],
        // 회원정보수정 : form onsubmit 기본 함수 선언
        \App\Modules\Events\DefaultOnsubmitEditScript::class => [
            \App\Modules\Listeners\DefaultOnsubmitEditScriptListener::class,
        ],
        // 회원가입 : form onsubmit 기본 함수 선언
        \App\Modules\Events\DefaultOnsubmitRegisterScript::class => [
            \App\Modules\Listeners\DefaultOnsubmitRegisterScriptListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
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
