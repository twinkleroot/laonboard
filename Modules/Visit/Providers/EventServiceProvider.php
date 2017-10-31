<?php

namespace Modules\Visit\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Visit\Events\PushVisitStatus;
use Modules\Visit\Events\TestEvent1;
use Modules\Visit\Listeners\AddVisitStatus;
use Modules\Visit\Listeners\TestListener1;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        PushVisitStatus::class => [
            AddVisitStatus::class,
        ],
        TestEvent1::class => [
            TestListener1::class,
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
