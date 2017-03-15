<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Event;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		// 'App\Events\SomeEvent' => [
		// 	'App\Listeners\EventListener',
		// ],

		'\Illuminate\Auth\Events\Login' => [
        	'App\Listeners\LoginSuccessful',
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

		Event::listen(
			\Illuminate\Auth\Events\Login::class,
			\App\Listeners\LoginSuccessful::class
		);
	}
}
