<?php

namespace Modules\Visit\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Visit\Events\AddVisitStatus;
use Modules\Visit\Listeners\AddVisitStatusListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Modules\Visit\Events\AddVisitStatus::class => [
            \Modules\Visit\Listeners\AddVisitStatusListener::class,
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
