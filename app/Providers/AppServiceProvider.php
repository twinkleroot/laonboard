<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Blade Template 의 지시어 'date' 생성
		Blade::directive('date', function($expression) {
			return "<?php echo ($expression)->format('Y/m/d'); ?>";
		});

		// Blade Template 의 지시어 'datetime' 생성
		Blade::directive('datetime', function($expression) {
			return "<?php echo ($expression)->format('Y/m/d H:i'); ?>";
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
