<?php

namespace Modules\PopularSearches\Providers;

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
        // 메인 하단에 인기검색어 목록 표시
        \Modules\PopularSearches\Events\AddPopularSearchListToMain::class => [
            \Modules\PopularSearches\Listeners\AddPopularSearchListToMainListener::class,
        ],
        // 인기 검색어 추가
        \Modules\PopularSearches\Events\AddPopularKeyword::class => [
            \Modules\PopularSearches\Listeners\AddPopularKeywordListener::class,
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
