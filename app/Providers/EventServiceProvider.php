<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Naver\NaverExtendSocialite;

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

		// 로그인 성공 후 이벤트
		'\Illuminate\Auth\Events\Login' => [
        	'App\Listeners\LoginSuccessful',
    	],

		// 소셜 로그인 : 네이버
		SocialiteWasCalled::class => [
        	NaverExtendSocialite::class,
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
